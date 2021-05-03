<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Importer;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Countable;
use InvalidArgumentException;
use Pimcore;
use Pimcore\File;
use Pimcore\Mail;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Document;
use Pimcore\Model\Factory;
use Pimcore\Model\Version;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Throwable;
use Wvision\Bundle\DataDefinitionsBundle\Event\EventDispatcherInterface;
use Wvision\Bundle\DataDefinitionsBundle\Exception\DoNotSetException;
use Wvision\Bundle\DataDefinitionsBundle\Exception\UnexpectedValueException;
use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Loader\LoaderInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\ParamsAwareInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportProviderInterface;
use Wvision\Bundle\DataDefinitionsBundle\Runner\RunnerInterface;
use Wvision\Bundle\DataDefinitionsBundle\Runner\SaveRunnerInterface;
use Wvision\Bundle\DataDefinitionsBundle\Runner\SetterRunnerInterface;
use Wvision\Bundle\DataDefinitionsBundle\Setter\SetterInterface;

final class Importer implements ImporterInterface
{
    private ServiceRegistryInterface $providerRegistry;
    private ServiceRegistryInterface $filterRegistry;
    private ServiceRegistryInterface $runnerRegistry;
    private ServiceRegistryInterface $interpreterRegistry;
    private ServiceRegistryInterface $setterRegistry;
    private ServiceRegistryInterface $cleanerRegistry;
    private ServiceRegistryInterface $loaderRegistry;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;
    private Factory $modelFactory;
    private ExpressionLanguage $expressionLanguage;
    private bool $shouldStop = false;

    public function __construct(
        ServiceRegistryInterface $providerRegistry,
        ServiceRegistryInterface $filterRegistry,
        ServiceRegistryInterface $runnerRegistry,
        ServiceRegistryInterface $interpreterRegistry,
        ServiceRegistryInterface $setterRegistry,
        ServiceRegistryInterface $cleanerRegistry,
        ServiceRegistryInterface $loaderRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        Factory $modelFactory,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->providerRegistry = $providerRegistry;
        $this->filterRegistry = $filterRegistry;
        $this->runnerRegistry = $runnerRegistry;
        $this->interpreterRegistry = $interpreterRegistry;
        $this->setterRegistry = $setterRegistry;
        $this->cleanerRegistry = $cleanerRegistry;
        $this->loaderRegistry = $loaderRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->modelFactory = $modelFactory;
        $this->expressionLanguage = $expressionLanguage;
    }

    public function doImport(ImportDefinitionInterface $definition, $params): array
    {
        $filter = null;

        if ($definition->getCreateVersion()) {
            Version::enable();
        } else {
            Version::disable();
        }

        $filterType = $definition->getFilter();
        if ($filterType) {
            /**
             * @var FilterInterface $filter
             */
            $filter = $this->filterRegistry->get($filterType);
        }

        /** @var ImportDataSetInterface|array $data */
        $data = $this->getData($definition, $params);

        if ((\is_countable($data) || $data instanceof Countable) && \count($data) > 0) {
            $this->eventDispatcher->dispatch($definition, 'data_definitions.import.total', \count($data), $params);
        }

        [$objectIds, $exceptions] = $this->runImport($definition, $params, $filter, $data);

        $cleanerType = $definition->getCleaner();
        if ($cleanerType) {
            $cleaner = $this->cleanerRegistry->get($cleanerType);

            $this->logger->info(sprintf('Running Cleaner "%s"', $cleanerType));
            $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status',
                sprintf('Running Cleaner "%s"', $cleanerType));

            if ($cleaner instanceof DataSetAwareInterface) {
                $cleaner->setDataSet($data);
            }

            if ($cleaner instanceof ParamsAwareInterface) {
                $cleaner->setParams($params);
            }

            if ($cleaner instanceof LoggerAwareInterface) {
                $cleaner->setLogger($this->logger);
            }

            $cleaner->cleanup($definition, $objectIds);

            $this->logger->info(sprintf('Finished Cleaner "%s"', $cleanerType));
            $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status',
                sprintf('Finished Cleaner "%s"', $cleanerType));
        }

        if (count($exceptions) > 0) {
            $this->processFailedImport($definition, $params, $objectIds, $exceptions);
        } else {
            $this->processSuccessfullImport($definition, $params, $objectIds, $exceptions);
        }

        $this->eventDispatcher->dispatch($definition, 'data_definitions.import.finished', '', $params);

        return $objectIds;
    }

    public function processSuccessfullImport(ImportDefinitionInterface $definition, $params, $objectIds, $exceptions)
    {
        $this->sendDocument($definition, Document::getById($definition->getSuccessNotificationDocument()), $objectIds, $exceptions);
        $this->eventDispatcher->dispatch($definition, 'data_definitions.import.success', $params);
    }

    public function processFailedImport(ImportDefinitionInterface $definition, $params, $objectIds, $exceptions)
    {
        $this->sendDocument(
            $definition,
            Document::getById($definition->getFailureNotificationDocument()),
            $objectIds,
            $exceptions
        );
        $this->eventDispatcher->dispatch($definition, 'data_definitions.import.failure', $params);
    }

    public function stop() : void
    {
        $this->shouldStop = true;
    }

    private function sendDocument(
        ImportDefinitionInterface $definition,
        ?Document $document,
        array $objectIds,
        array $exceptions
    )
    {
        if ($document instanceof Document) {
            $params = [
                'exceptions' => $exceptions,
                'objectIds' => $objectIds,
                'className' => $definition->getClass(),
                'countObjects' => count($objectIds),
                'countExceptions' => count($exceptions),
                'name' => $definition->getName(),
                'provider' => $definition->getProvider(),
            ];

            if ($document instanceof Document\Email) {
                $mail = new Mail();
                $mail->setDocument($document);
                $mail->setParams($params);

                $mail->send();
            } elseif (is_a($document, "\\Pimcore\\Model\\Document\\Pushover")) {
                $document->send($params);
            }
        }
    }

    private function getData(ImportDefinitionInterface $definition, array $params)
    {
        /** @var ImportProviderInterface $provider */
        $provider = $this->providerRegistry->get($definition->getProvider());

        return $provider->getData($definition->getConfiguration(), $definition, $params);
    }

    private function runImport(
        ImportDefinitionInterface $definition,
        array $params,
        FilterInterface $filter = null,
        ?array $dataSet = null
    ): array
    {
        if (null === $dataSet) {
            $dataSet = [];
        }

        $count = 0;
        $countToClean = 1000;
        $objectIds = [];
        $exceptions = [];

        foreach ($dataSet as $row) {
            try {
                $object = $this->importRow($definition, $row, $dataSet, array_merge($params, ['row' => $count]), $filter);

                if ($object instanceof Concrete) {
                    $objectIds[] = $object->getId();
                }

                if (($count + 1) % $countToClean === 0) {
                    Pimcore::collectGarbage();
                    $this->logger->info('Clean Garbage');
                    $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status', 'Collect Garbage',
                        $params);
                }

                $count++;
            } catch (Throwable $ex) {
                $this->logger->error($ex);

                $exceptions[] = $ex;

                $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status',
                    sprintf('Error: %s', $ex->getMessage()), $params);

                if ($definition->getStopOnException()) {
                    throw $ex;
                }
            }

            $this->eventDispatcher->dispatch($definition, 'data_definitions.import.progress', '', $params);

            if ($this->shouldStop) {
                $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status',
                    'Process has been stopped.');
                return [$objectIds, $exceptions];
            }
        }

        return [$objectIds, $exceptions];
    }

    private function importRow(
        ImportDefinitionInterface $definition,
        array $data,
        array $dataSet,
        array $params,
        FilterInterface $filter = null
    ): ?Concrete
    {
        if (null === $data) {
            return null;
        }
        $runner = null;

        $object = $this->getObject($definition, $data, $params);

        if (null !== $object && !$object->getId()) {
            if ($definition->getSkipNewObjects()) {
                $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status', 'Ignoring new Object',
                    $params);

                return null;
            }
        } else if ($definition->getSkipExistingObjects()) {
            $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status', 'Ignoring existing Object',
                $params);

            return null;
        }

        if ($filter instanceof FilterInterface) {
            if ($filter instanceof DataSetAwareInterface) {
                $filter->setDataSet($dataSet);
            }

            if ($filter instanceof LoggerAwareInterface) {
                $filter->setLogger($this->logger);
            }

            if (!$filter->filter($definition, $data, $object)) {
                $this->eventDispatcher->dispatch($definition, 'data_definitions.import.status', 'Filtered Object', $params);

                return null;
            }
        }

        $this->eventDispatcher->dispatch(
            $definition,
            'data_definitions.import.status',
            sprintf('Import Object %s', ($object->getId() ? $object->getFullPath() : 'new')),
            $params
        );
        $this->eventDispatcher->dispatch(
            $definition,
            'data_definitions.import.object.start',
            $object,
            $params
        );

        if ($definition->getRunner()) {
            $runner = $this->runnerRegistry->get($definition->getRunner());
        }


        if ($runner instanceof RunnerInterface) {
            if ($runner instanceof DataSetAwareInterface) {
                $runner->setDataSet($dataSet);
            }

            if ($runner instanceof LoggerAwareInterface) {
                $runner->setLogger($this->logger);
            }

            $runner->preRun($object, $data, $definition, $params);
        }

        $this->logger->info(sprintf('Imported Object: %s', $object->getRealFullPath()));

        /** @var $mapItem ImportMapping */
        foreach ($definition->getMapping() as $mapItem) {
            $value = null;

            if (array_key_exists($mapItem->getFromColumn(), $data) || $mapItem->getFromColumn() === "custom") {
                $value = $data[$mapItem->getFromColumn()];
                $this->setObjectValue($object, $mapItem, $value, $data, $dataSet, $definition, $params, $runner);
            }
        }

        $shouldSave = true;
        if ($runner instanceof SaveRunnerInterface) {
            if ($runner instanceof DataSetAwareInterface) {
                $runner->setDataSet($dataSet);
            }

            if ($runner instanceof LoggerAwareInterface) {
                $runner->setLogger($this->logger);
            }

            $shouldSave = $runner->shouldSaveObject($object, $data, $definition, $params);
        }

        if ($shouldSave) {
            $object->setUserModification($params['userId'] ?? 0);
            $object->setOmitMandatoryCheck($definition->getOmitMandatoryCheck());
            $object->save();

            $this->eventDispatcher->dispatch(
                $definition,
                'data_definitions.import.status',
                sprintf('Imported Object %s', $object->getFullPath()),
                $params
            );
        } else {
            $this->eventDispatcher->dispatch(
                $definition,
                'data_definitions.import.status',
                sprintf('Skipped Object %s', $object->getFullPath()),
                $params
            );
        }

        $this->eventDispatcher->dispatch(
            $definition,
            'data_definitions.import.status',
            sprintf('Imported Object %s', $object->getFullPath()),
            $params
        );
        $this->eventDispatcher->dispatch(
            $definition,
            'data_definitions.import.object.finished',
            $object,
            $params
        );

        if ($runner instanceof RunnerInterface) {
            if ($runner instanceof DataSetAwareInterface) {
                $runner->setDataSet($dataSet);
            }

            if ($runner instanceof LoggerAwareInterface) {
                $runner->setLogger($this->logger);
            }

            $runner->postRun($object, $data, $definition, $params);
        }

        return $object;
    }

    private function setObjectValue(
        Concrete $object,
        ImportMapping $map,
        $value,
        array $data,
        $dataSet,
        ImportDefinitionInterface $definition,
        array $params,
        RunnerInterface $runner = null
    ): void {
        if ($map->getInterpreter()) {
            try {
                $interpreter = $this->interpreterRegistry->get($map->getInterpreter());

                if ($interpreter instanceof DataSetAwareInterface) {
                    $interpreter->setDataSet($dataSet);
                }

                if ($interpreter instanceof LoggerAwareInterface) {
                    $interpreter->setLogger($this->logger);
                }

                try {
                    $value = $interpreter->interpret(
                        $object,
                        $value,
                        $map,
                        $data,
                        $definition,
                        $params,
                        $map->getInterpreterConfig()
                    );
                }
                catch (UnexpectedValueException $ex) {
                    $this->logger->info(sprintf('Unexpected Value from Interpreter "%s" with message "%s"', $map->getInterpreter(), $ex->getMessage()));
                }

            } catch (DoNotSetException $ex) {
                return;
            }

        }

        if ($map->getToColumn() === 'o_type' && $map->getSetter() !== 'object_type') {
            throw new InvalidArgumentException('Type has to be used with ObjectType Setter!');
        }

        $shouldSetField = true;

        if ($runner instanceof SetterRunnerInterface) {
            if ($runner instanceof DataSetAwareInterface) {
                $runner->setDataSet($dataSet);
            }

            if ($runner instanceof LoggerAwareInterface) {
                $runner->setLogger($this->logger);
            }

            $shouldSetField = $runner->shouldSetField($object, $map, $value, $data, $definition, $params);
        }

        if (!$shouldSetField) {
            return;
        }

        if ($map->getSetter()) {
            $setter = $this->setterRegistry->get($map->getSetter());

            if ($setter instanceof SetterInterface) {
                if ($setter instanceof DataSetAwareInterface) {
                    $setter->setDataSet($dataSet);
                }

                if ($setter instanceof LoggerAwareInterface) {
                    $setter->setLogger($this->logger);
                }

                $setter->set($object, $value, $map, $data);
            }
        } else {
            $object->setValue($map->getToColumn(), $value);
        }
    }

    private function getObject(ImportDefinitionInterface $definition, $data, $params): Concrete
    {
        $class = $definition->getClass();
        $classObject = '\Pimcore\Model\DataObject\\'.ucfirst($class);
        $classDefinition = ClassDefinition::getByName($class);

        if (!$classDefinition instanceof ClassDefinition) {
            throw new InvalidArgumentException(sprintf('Class not found %s', $class));
        }

        /**
         * @var $loader LoaderInterface
         */
        if ($definition->getLoader()) {
            $loader = $this->loaderRegistry->get($definition->getLoader());
        } else {
            $loader = $this->loaderRegistry->get('primary_key');
        }

        $obj = $loader->load($class, $data, $definition, $params);

        if (null === $obj) {
            $classImplementation = $this->modelFactory->getClassNameFor($classObject) ?? $classObject;
            $obj = new $classImplementation();
        }

        $key = Service::getValidKey($this->createKey($definition, $data), 'object');

        if ($definition->getRelocateExistingObjects() || !$obj->getId()) {
            $obj->setParent(Service::createFolderByPath($this->createPath($definition, $data)));
        }

        if ($definition->getRenameExistingObjects() || !$obj->getId()) {
            if ($key && $definition->getKey()) {
                $obj->setKey($key);
            } else {
                $obj->setKey(File::getValidFilename(uniqid('', true)));
            }
        }

        if (!$obj->getKey()) {
            throw new InvalidArgumentException('No key set, please check your import-data');
        }

        $obj->setKey(Service::getUniqueKey($obj));

        return $obj;
    }

    private function createPath(ImportDefinitionInterface $definition, array $data): string
    {
        if (str_starts_with($definition->getObjectPath(), '@')) {
            return $this->expressionLanguage->evaluate($definition->getObjectPath(), $data);
        }

        return $definition->getObjectPath() ?? '';
    }

    private function createKey(ImportDefinitionInterface $definition, array $data): string
    {
        if (str_starts_with($definition->getKey(), '@')) {
            return $this->expressionLanguage->evaluate($definition->getKey(), $data);
        }

        return $definition->getKey() ?? '';
    }
}
