<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Exporter;

use CoreShop\Component\Pimcore\DataObject\UnpublishedHelper;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Exception;
use Instride\Bundle\DataDefinitionsBundle\Context\ContextFactoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\FetcherContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Event\ExportDefinitionEvent;
use Instride\Bundle\DataDefinitionsBundle\Exception\UnexpectedValueException;
use Instride\Bundle\DataDefinitionsBundle\Fetcher\FetcherInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\DynamicColumnGetterInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Instride\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Instride\Bundle\DataDefinitionsBundle\Provider\ExportProviderInterface;
use Instride\Bundle\DataDefinitionsBundle\Runner\ExportRunnerInterface;
use InvalidArgumentException;
use function is_array;
use Pimcore;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Exporter implements ExporterInterface
{
    private bool $shouldStop = false;

    private array $exceptions = [];

    public function __construct(
        private ServiceRegistryInterface $fetcherRegistry,
        private ServiceRegistryInterface $runnerRegistry,
        private ServiceRegistryInterface $interpreterRegistry,
        private ServiceRegistryInterface $getterRegistry,
        private ServiceRegistryInterface $exportProviderRegistry,
        private ContextFactoryInterface $contextFactory,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function doExport(ExportDefinitionInterface $definition, array $params)
    {
        $fetcherContext = $this->contextFactory->createFetcherContext($definition, $params, is_array($definition->getFetcherConfig()) ? $definition->getFetcherConfig() : []);

        $fetcher = $this->getFetcher($definition);
        $provider = $this->getProvider($definition);
        $total = $fetcher->count($fetcherContext);

        $this->eventDispatcher->dispatch(
            new ExportDefinitionEvent($definition, $total, $params),
            'data_definitions.export.total',
        );

        $this->runExport($definition, $params, $total, $fetcherContext, $fetcher, $provider);

        $this->eventDispatcher->dispatch(
            new ExportDefinitionEvent($definition, null, $params),
            'data_definitions.export.finished',
        );
    }

    private function getFetcher(ExportDefinitionInterface $definition): FetcherInterface
    {
        if (!$this->fetcherRegistry->has($definition->getFetcher())) {
            throw new InvalidArgumentException(
                sprintf(
                    'Export Definition %s has no valid fetcher configured',
                    $definition->getName(),
                ),
            );
        }

        /** @var FetcherInterface $fetcher */
        $fetcher = $this->fetcherRegistry->get($definition->getFetcher());

        return $fetcher;
    }

    private function getProvider(ExportDefinitionInterface $definition): ExportProviderInterface
    {
        if (!$this->exportProviderRegistry->has($definition->getProvider())) {
            throw new InvalidArgumentException(
                sprintf(
                    'Definition %s has no valid export provider configured',
                    $definition->getName(),
                ),
            );
        }

        return $this->exportProviderRegistry->get($definition->getProvider());
    }

    private function runExport(
        ExportDefinitionInterface $definition,
        $params,
        int $total,
        FetcherContextInterface $fetcherContext,
        FetcherInterface $fetcher,
        ExportProviderInterface $provider,
    ) {
        $getInheritedValues = DataObject::getGetInheritedValues();
        DataObject::setGetInheritedValues($definition->isEnableInheritance());

        UnpublishedHelper::hideUnpublished(
            function () use ($definition, $params, $total, $fetcher, $provider, $fetcherContext) {
                $count = 0;
                $perLoop = 50;
                $perRun = ceil($total / $perLoop);

                for ($i = 0; $i < $perRun; ++$i) {
                    $objects = $fetcher->fetch(
                        $fetcherContext,
                        $perLoop,
                        $i * $perLoop,
                    );

                    foreach ($objects as $object) {
                        try {
                            $this->exportRow($definition, $object, $params, $provider);

                            if (($count + 1) % $perLoop === 0) {
                                Pimcore::collectGarbage();
                                $this->logger->info('Clean Garbage');
                                $this->eventDispatcher->dispatch(
                                    new ExportDefinitionEvent($definition, 'Collect Garbage', $params),
                                    'data_definitions.export.status',
                                );
                            }

                            ++$count;
                        } catch (Exception $ex) {
                            $this->logger->error($ex);

                            $this->exceptions[] = $ex;

                            $this->eventDispatcher->dispatch(
                                new ExportDefinitionEvent(
                                    $definition,
                                    sprintf('Error: %s', $ex->getMessage()),
                                    $params,
                                ),
                                'data_definitions.export.status',
                            );

                            if ($definition->getStopOnException()) {
                                throw $ex;
                            }
                        }

                        $this->eventDispatcher->dispatch(
                            new ExportDefinitionEvent($definition, null, $params),
                            'data_definitions.export.progress',
                        );
                    }

                    if ($this->shouldStop) {
                        $this->eventDispatcher->dispatch(
                            new ExportDefinitionEvent($definition, 'Process has been stopped.', $params),
                            'data_definitions.export.status',
                        );

                        return;
                    }
                }
                $provider->exportData($definition->getConfiguration(), $definition, $params);
            },
            false === $definition->isFetchUnpublished(),
        );

        DataObject::setGetInheritedValues($getInheritedValues);
    }

    private function exportRow(
        ExportDefinitionInterface $definition,
        Concrete $object,
        $params,
        ExportProviderInterface $provider,
    ): array {
        $data = [];

        $runner = null;
        $runnerContext = $this->contextFactory->createRunnerContext($definition, $params, null, null, $object);

        $this->eventDispatcher->dispatch(
            new ExportDefinitionEvent($definition, sprintf('Export Object %s', $object->getId()), $params),
            'data_definitions.export.status',
        );
        $this->eventDispatcher->dispatch(
            new ExportDefinitionEvent($definition, $object, $params),
            'data_definitions.export.object.start',
        );

        if ($definition->getRunner()) {
            $runner = $this->runnerRegistry->get($definition->getRunner());
        }

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPreRun($runnerContext);
        }

        $this->logger->info(sprintf('Export Object: %s', $object->getRealFullPath()));

        if (is_array($definition->getMapping())) {
            /**
             * @var ExportMapping $mapItem
             */
            foreach ($definition->getMapping() as $mapItem) {
                $getter = $this->fetchGetter($mapItem);
                $value = $this->getObjectValue(
                    $object,
                    $mapItem,
                    $data,
                    $definition,
                    $params,
                    $getter,
                );

                if ($getter instanceof DynamicColumnGetterInterface) {
                    $data = array_merge($data, $value);
                } else {
                    $data[$mapItem->getToColumn()] = $value;
                }
            }
        }

        $provider->addExportData($data, $definition->getConfiguration(), $definition, $params);

        $this->eventDispatcher->dispatch(
            new ExportDefinitionEvent($definition, sprintf('Exported Object %s', $object->getFullPath()), $params),
            'data_definitions.export.status',
        );
        $this->eventDispatcher->dispatch(
            new ExportDefinitionEvent($definition, $object, $params),
            'data_definitions.export.object.finished',
        );

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPostRun($runnerContext);
        }

        return $data;
    }

    private function getObjectValue(
        Concrete $object,
        ExportMapping $map,
        $data,
        ExportDefinitionInterface $definition,
        $params,
        ?GetterInterface $getter,
    ) {
        $value = null;

        if (null !== $getter) {
            $getterContext = $this->contextFactory->createGetterContext($definition, $params, $object, $map);
            $value = $getter->get($getterContext);
        } else {
            $getter = 'get' . ucfirst($map->getFromColumn());

            if (method_exists($object, $getter)) {
                $value = $object->$getter();
            }
        }

        if ($map->getInterpreter()) {
            $interpreter = $this->interpreterRegistry->get($map->getInterpreter());

            if ($interpreter instanceof InterpreterInterface) {
                try {
                    $context = $this->contextFactory->createInterpreterContext(
                        $definition,
                        $params,
                        $map->getInterpreterConfig(),
                        $data,
                        null,
                        $object,
                        $value,
                        $map,
                    );
                    $value = $interpreter->interpret($context);
                } catch (UnexpectedValueException $ex) {
                    $this->logger->info(
                        sprintf(
                            'Unexpected Value from Interpreter "%s" with message "%s"',
                            $map->getInterpreter(),
                            $ex->getMessage(),
                        ),
                    );
                }
            }
        }

        return $value;
    }

    private function fetchGetter(ExportMapping $map): ?GetterInterface
    {
        if ($name = $map->getGetter()) {
            $getter = $this->getterRegistry->get($name);
            if ($getter instanceof GetterInterface) {
                return $getter;
            }
        }

        return null;
    }

    public function stop(): void
    {
        $this->shouldStop = true;
    }
}
