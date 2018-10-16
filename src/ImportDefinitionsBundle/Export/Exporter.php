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

namespace ImportDefinitionsBundle\Export;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use ImportDefinitionsBundle\Fetcher\FetcherInterface;
use ImportDefinitionsBundle\Getter\GetterInterface;
use ImportDefinitionsBundle\Interpreter\ReverseInterpreterInterface;
use ImportDefinitionsBundle\Exception\DoNotSetException;
use ImportDefinitionsBundle\Runner\ExportRunnerInterface;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ImportDefinitionsBundle\Event\ImportDefinitionEvent;
use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use ImportDefinitionsBundle\Runner\RunnerInterface;

final class Exporter implements ExportInterface
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
    private $reverseInterpreterRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $getterRegistry;

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
     * @var string
     */
    private $artifact;

    /**
     * Importer constructor.
     * @param ServiceRegistryInterface $fetcherRegistry
     * @param ServiceRegistryInterface $runnerRegistry
     * @param ServiceRegistryInterface $reverseInterpreterRegistry
     * @param ServiceRegistryInterface $getterRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ServiceRegistryInterface $fetcherRegistry,
        ServiceRegistryInterface $runnerRegistry,
        ServiceRegistryInterface $reverseInterpreterRegistry,
        ServiceRegistryInterface $getterRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    )
    {
        $this->fetcherRegistry = $fetcherRegistry;
        $this->runnerRegistry = $runnerRegistry;
        $this->reverseInterpreterRegistry = $reverseInterpreterRegistry;
        $this->getterRegistry = $getterRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function doExport(DefinitionInterface $definition, $params)
    {
        $fetcher = $this->getFetcher($definition);
        $total = $fetcher->count($definition, $params);

        if ($total > 0) {
            $this->eventDispatcher->dispatch('export_definition.total', new ImportDefinitionEvent($definition, $total));

            $this->artifact = tempnam(PIMCORE_TEMPORARY_DIRECTORY, 'export_definition_artifact');
            $this->runExport($definition, $params, $total, $fetcher);
        }

        $this->eventDispatcher->dispatch('export_definition.artifact', new ImportDefinitionEvent($definition, $this->artifact));
        $this->eventDispatcher->dispatch('export_definition.finished', new ImportDefinitionEvent($definition));
    }

    /**
     * @param DefinitionInterface $definition
     * @return FetcherInterface
     */
    private function getFetcher(DefinitionInterface $definition)
    {
        if (!$this->fetcherRegistry->has($definition->getFetcher())) {
            throw new \InvalidArgumentException(sprintf('Definition %s has no valid fetcher configured', $definition->getName()));
        }

        /** @var FetcherInterface $fetcher */
        $fetcher = $this->fetcherRegistry->get($definition->getFetcher());

        return $fetcher;
    }

    /**
     * @param DefinitionInterface $definition
     * @param                     $params
     * @param int                 $total
     * @param FetcherInterface    $fetcher
     * @throws \Exception
     */
    private function runExport(DefinitionInterface $definition, $params, int $total, FetcherInterface $fetcher)
    {
        $count = 0;
        $countToClean = 1000;
        $perLoop = 50;

        $entries = [];

        for ($i = 0; $i < (ceil($total / $perLoop)); $i++) {

            $objects = $fetcher->fetch($definition, $params, $perLoop, $i * $perLoop);

            foreach ($objects as $object) {
                try {
                    $entries[] = $this->exportRow($definition, $object, $params);

                    if (($count + 1) % $countToClean === 0) {
                        \Pimcore::collectGarbage();
                        $this->logger->info('Clean Garbage');
                        $this->eventDispatcher->dispatch(
                            'export_definition.status',
                            new ImportDefinitionEvent($definition, 'Collect Garbage')
                        );
                    }

                    $count++;
                } catch (\Exception $ex) {
                    $this->logger->error($ex);

                    $this->exceptions[] = $ex;

                    $this->eventDispatcher->dispatch(
                        'export_definition.status',
                        new ImportDefinitionEvent($definition, sprintf('Error: %s', $ex->getMessage()))
                    );

                    if ($definition->getStopOnException()) {
                        throw $ex;
                    }
                }

                $this->eventDispatcher->dispatch(
                    'export_definition.progress',
                    new ImportDefinitionEvent($definition)
                );
            }
        }
    }

    /**
     * @param DefinitionInterface $definition
     * @param Concrete $object
     * @param $params
     * @return null|Concrete
     * @throws \Exception
     */
    private function exportRow(DefinitionInterface $definition, Concrete $object, $params): array
    {
        $data = [];

        $runner = null;

        $this->eventDispatcher->dispatch('export_definition.status', new ImportDefinitionEvent($definition, sprintf('Export Object %s', $object->getId())));
        $this->eventDispatcher->dispatch('export_definition.object.start', new ImportDefinitionEvent($definition, $object));

        if ($definition->getRunner()) {
            $runner = $this->runnerRegistry->get($definition->getRunner());
        }

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPreRun($object, $data, $definition, $params);
        }

        $this->logger->info(sprintf('Export Object: %s', $object->getRealFullPath()));

        foreach ($definition->getMapping() as $mapItem)
        {
            $data[$mapItem->getFromColumn()] = $this->getObjectValue($object, $mapItem, $data, $definition, $params, $runner);
        }

        $this->eventDispatcher->dispatch('export_definition.status', new ImportDefinitionEvent($definition, sprintf('Exported Object %s', $object->getFullPath())));
        $this->eventDispatcher->dispatch('export_definition.object.finished', new ImportDefinitionEvent($definition, $object));

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPostRun($object, $data, $definition, $params);
        }

        return $data;
    }

    /**
     * @param Concrete             $object
     * @param Mapping              $map
     * @param                      $data
     * @param DefinitionInterface  $definition
     * @param                      $params
     * @param RunnerInterface|null $runner
     * @return mixed|null
     * @throws DoNotSetException
     */
    private function getObjectValue(Concrete $object, Mapping $map, $data, DefinitionInterface $definition, $params, RunnerInterface $runner = null)
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

        if ($map->getReverseInterpreter()) {
            $interpreter = $this->reverseInterpreterRegistry->get($map->getReverseInterpreter());

            if ($interpreter instanceof ReverseInterpreterInterface) {
                $value = $interpreter->reverseInterpret($object, $value, $map, $data, $definition, $params, $map->getReverseInterpreterConfig());
            }

        }

        return $value;
    }
}
