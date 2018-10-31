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
use ImportDefinitionsBundle\Exception\DoNotSetException;
use ImportDefinitionsBundle\Interpreter\InterpreterInterface;
use ImportDefinitionsBundle\Model\ExportMapping;
use ImportDefinitionsBundle\Runner\ExportRunnerInterface;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ImportDefinitionsBundle\Event\ExportDefinitionEvent;
use ImportDefinitionsBundle\Model\ExportDefinitionInterface;

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
    private $interpreterRegistry;

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
     * Importer constructor.
     * @param ServiceRegistryInterface $fetcherRegistry
     * @param ServiceRegistryInterface $runnerRegistry
     * @param ServiceRegistryInterface $interpreterRegistry
     * @param ServiceRegistryInterface $getterRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ServiceRegistryInterface $fetcherRegistry,
        ServiceRegistryInterface $runnerRegistry,
        ServiceRegistryInterface $interpreterRegistry,
        ServiceRegistryInterface $getterRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    )
    {
        $this->fetcherRegistry = $fetcherRegistry;
        $this->runnerRegistry = $runnerRegistry;
        $this->interpreterRegistry = $interpreterRegistry;
        $this->getterRegistry = $getterRegistry;
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
        $total = $fetcher->count($definition, $params);

        if ($total > 0) {
            $this->eventDispatcher->dispatch('export_definition.total', new ExportDefinitionEvent($definition, $total));

            $this->runExport($definition, $params, $total, $fetcher);
        }

        $this->eventDispatcher->dispatch('export_definition.finished', new ExportDefinitionEvent($definition));
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
     * @param                     $params
     * @param int                 $total
     * @param FetcherInterface    $fetcher
     * @throws \Exception
     */
    private function runExport(ExportDefinitionInterface $definition, $params, int $total, FetcherInterface $fetcher)
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
                            new ExportDefinitionEvent($definition, 'Collect Garbage')
                        );
                    }

                    $count++;
                } catch (\Exception $ex) {
                    $this->logger->error($ex);

                    $this->exceptions[] = $ex;

                    $this->eventDispatcher->dispatch(
                        'export_definition.status',
                        new ExportDefinitionEvent($definition, sprintf('Error: %s', $ex->getMessage()))
                    );

                    if ($definition->getStopOnException()) {
                        throw $ex;
                    }
                }

                $this->eventDispatcher->dispatch(
                    'export_definition.progress',
                    new ExportDefinitionEvent($definition)
                );
            }
        }

        print_r($entries);
    }

    /**
     * @param ExportDefinitionInterface $definition
     * @param Concrete $object
     * @param $params
     * @return array
     * @throws \Exception
     */
    private function exportRow(ExportDefinitionInterface $definition, Concrete $object, $params): array
    {
        $data = [];

        $runner = null;

        $this->eventDispatcher->dispatch('export_definition.status', new ExportDefinitionEvent($definition, sprintf('Export Object %s', $object->getId())));
        $this->eventDispatcher->dispatch('export_definition.object.start', new ExportDefinitionEvent($definition, $object));

        if ($definition->getRunner()) {
            $runner = $this->runnerRegistry->get($definition->getRunner());
        }

        if ($runner instanceof ExportRunnerInterface) {
            $data = $runner->exportPreRun($object, $data, $definition, $params);
        }

        $this->logger->info(sprintf('Export Object: %s', $object->getRealFullPath()));

        /**
         * @var $mapItem ExportMapping
         */
        foreach ($definition->getMapping() as $mapItem)
        {
            $data[$mapItem->getFromColumn()] = $this->getObjectValue($object, $mapItem, $data, $definition, $params);
        }

        $this->eventDispatcher->dispatch('export_definition.status', new ExportDefinitionEvent($definition, sprintf('Exported Object %s', $object->getFullPath())));
        $this->eventDispatcher->dispatch('export_definition.object.finished', new ExportDefinitionEvent($definition, $object));

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