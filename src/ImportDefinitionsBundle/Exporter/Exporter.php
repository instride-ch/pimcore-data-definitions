<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Exporter;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use ImportDefinitionsBundle\Event\ExportDefinitionEvent;
use ImportDefinitionsBundle\Exception\DoNotSetException;
use ImportDefinitionsBundle\Fetcher\FetcherInterface;
use ImportDefinitionsBundle\Getter\GetterInterface;
use ImportDefinitionsBundle\Interpreter\InterpreterInterface;
use ImportDefinitionsBundle\Model\ExportDefinitionInterface;
use ImportDefinitionsBundle\Model\ExportMapping;
use ImportDefinitionsBundle\Provider\ExportProviderInterface;
use ImportDefinitionsBundle\Runner\ExportRunnerInterface;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Exporter implements ExporterInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $fetcherRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $runnerRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $interpreterRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $getterRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $exportProviderRegistry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $exceptions = [];

    /**
     * @param ServiceRegistryInterface $fetcherRegistry
     * @param ServiceRegistryInterface $runnerRegistry
     * @param ServiceRegistryInterface $interpreterRegistry
     * @param ServiceRegistryInterface $getterRegistry
     * @param ServiceRegistryInterface $exportProviderRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(
        ServiceRegistryInterface $fetcherRegistry,
        ServiceRegistryInterface $runnerRegistry,
        ServiceRegistryInterface $interpreterRegistry,
        ServiceRegistryInterface $getterRegistry,
        ServiceRegistryInterface $exportProviderRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    )
    {
        $this->fetcherRegistry = $fetcherRegistry;
        $this->runnerRegistry = $runnerRegistry;
        $this->interpreterRegistry = $interpreterRegistry;
        $this->getterRegistry = $getterRegistry;
        $this->exportProviderRegistry = $exportProviderRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function doExport(ExportDefinitionInterface $definition, $params)
    {
        $fetcher = $this->getFetcher($definition);
        $provider = $this->getProvider($definition);
        $total = $fetcher->count($definition, $params, is_array($definition->getFetcherConfig()) ? $definition->getFetcherConfig() : []);

        if ($total > 0) {
            $this->eventDispatcher->dispatch('export_definition.total', new ExportDefinitionEvent($definition, $total, $params));

            $this->runExport($definition, $params, $total, $fetcher, $provider);
        }

        $this->eventDispatcher->dispatch('export_definition.finished', new ExportDefinitionEvent($definition, null, $params));
    }

    /**
     * @param ExportDefinitionInterface $definition
     * @return FetcherInterface
     */
    private function getFetcher(ExportDefinitionInterface $definition)
    {
        if (!$this->fetcherRegistry->has($definition->getFetcher())) {
            throw new \InvalidArgumentException(sprintf('Export Definition %s has no valid fetcher configured', $definition->getName()));
        }

        /** @var FetcherInterface $fetcher */
        $fetcher = $this->fetcherRegistry->get($definition->getFetcher());

        return $fetcher;
    }

    /**
     * @param ExportDefinitionInterface $definition
     * @return ExportProviderInterface
     */
    private function getProvider(ExportDefinitionInterface $definition)
    {
        if (!$this->exportProviderRegistry->has($definition->getProvider())) {
            throw new \InvalidArgumentException(sprintf('Definition %s has no valid export provider configured', $definition->getName()));
        }

        return $this->exportProviderRegistry->get($definition->getProvider());
    }

    /**
     * @param ExportDefinitionInterface $definition
     * @param                     $params
     * @param int                 $total
     * @param FetcherInterface    $fetcher
     * @param ExportProviderInterface $provider
     * @throws \Exception
     */
    private function runExport(ExportDefinitionInterface $definition, $params, int $total, FetcherInterface $fetcher, ExportProviderInterface $provider)
    {
        $count = 0;
        $countToClean = 1000;
        $perLoop = 50;

        for ($i = 0; $i < (ceil($total / $perLoop)); $i++) {

            $objects = $fetcher->fetch($definition, $params, $perLoop, $i * $perLoop, is_array($definition->getFetcherConfig()) ? $definition->getFetcherConfig() : []);

            foreach ($objects as $object) {
                try {
                    $this->exportRow($definition, $object, $params, $provider);
                    
                    if (($count + 1) % $countToClean === 0) {
                        \Pimcore::collectGarbage();
                        $this->logger->info('Clean Garbage');
                        $this->eventDispatcher->dispatch(
                            'export_definition.status',
                            new ExportDefinitionEvent($definition, 'Collect Garbage', $params)
                        );
                    }

                    $count++;
                } catch (\Exception $ex) {
                    $this->logger->error($ex);

                    $this->exceptions[] = $ex;

                    $this->eventDispatcher->dispatch(
                        'export_definition.status',
                        new ExportDefinitionEvent($definition, sprintf('Error: %s', $ex->getMessage()), $params)
                    );

                    if ($definition->getStopOnException()) {
                        throw $ex;
                    }
                }

                $this->eventDispatcher->dispatch(
                    'export_definition.progress',
                    new ExportDefinitionEvent($definition, null, $params)
                );
            }
        }

        $provider->exportData($definition->getConfiguration(), $definition, $params);
    }

    /**
     * @param ExportDefinitionInterface $definition
     * @param Concrete $object
     * @param $params
     * @param ExportProviderInterface $provider
     * @return array
     * @throws \Exception
     */
    private function exportRow(ExportDefinitionInterface $definition, Concrete $object, $params, ExportProviderInterface $provider): array
    {
        $data = [];

        $runner = null;

        $this->eventDispatcher->dispatch('export_definition.status', new ExportDefinitionEvent($definition, sprintf('Export Object %s', $object->getId()), $params));
        $this->eventDispatcher->dispatch('export_definition.object.start', new ExportDefinitionEvent($definition, $object, $params));

        if ($definition->getRunner()) {
            $runner = $this->runnerRegistry->get($definition->getRunner());
        }

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPreRun($object, $data, $definition, $params);
        }

        $this->logger->info(sprintf('Export Object: %s', $object->getRealFullPath()));

        if (is_array($definition->getMapping())) {
            /**
             * @var $mapItem ExportMapping
             */
            foreach ($definition->getMapping() as $mapItem) {
                $data[$mapItem->getFromColumn()] = $this->getObjectValue(
                    $object,
                    $mapItem,
                    $data,
                    $definition,
                    $params
                );
            }
        }
        
        $provider->addExportData($data, $definition->getConfiguration(), $definition, $params);

        $this->eventDispatcher->dispatch('export_definition.status', new ExportDefinitionEvent($definition, sprintf('Exported Object %s', $object->getFullPath()), $params));
        $this->eventDispatcher->dispatch('export_definition.object.finished', new ExportDefinitionEvent($definition, $object, $params));

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPostRun($object, $data, $definition, $params);
        }

        return $data;
    }

    /**
     * @param Concrete             $object
     * @param ExportMapping        $map
     * @param                      $data
     * @param ExportDefinitionInterface  $definition
     * @param                      $params
     * @return mixed|null
     * @throws DoNotSetException
     */
    private function getObjectValue(Concrete $object, ExportMapping $map, $data, ExportDefinitionInterface $definition, $params)
    {
        $value = null;

        if ($map->getGetter()) {
            $getter = $this->getterRegistry->get($map->getGetter());

            if ($getter instanceof GetterInterface) {
                $value = $getter->get($object, $map, $data);
            }
        } else {
            $getter = "get" . ucfirst($map->getToColumn());

            if (method_exists($object, $getter)) {
                $value = $object->$getter();
            }
        }

        if ($map->getInterpreter()) {
            $interpreter = $this->interpreterRegistry->get($map->getInterpreter());

            if ($interpreter instanceof InterpreterInterface) {
                $value = $interpreter->interpret($object, $value, $map, $data, $definition, $params, $map->getInterpreterConfig());
            }

        }

        return $value;
    }
}