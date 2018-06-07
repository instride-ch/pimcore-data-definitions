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

namespace ImportDefinitionsBundle\Importer;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\File;
use Pimcore\Mail;
use Pimcore\Model\Document;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Version;
use Pimcore\Placeholder;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ImportDefinitionsBundle\Event\ImportDefinitionEvent;
use ImportDefinitionsBundle\Filter\FilterInterface;
use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use ImportDefinitionsBundle\Provider\ProviderInterface;
use ImportDefinitionsBundle\Runner\RunnerInterface;

final class Importer implements ImporterInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $providerRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $filterRegistry;

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
    private $setterRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $cleanerRegistry;

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
    private $objectIds = [];

    /**
     * @var array
     */
    private $exceptions = [];

    /**
     * Importer constructor.
     * @param ServiceRegistryInterface $providerRegistry
     * @param ServiceRegistryInterface $filterRegistry
     * @param ServiceRegistryInterface $runnerRegistry
     * @param ServiceRegistryInterface $interpreterRegistry
     * @param ServiceRegistryInterface $setterRegistry
     * @param ServiceRegistryInterface $cleanerRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ServiceRegistryInterface $providerRegistry,
        ServiceRegistryInterface $filterRegistry,
        ServiceRegistryInterface $runnerRegistry,
        ServiceRegistryInterface $interpreterRegistry,
        ServiceRegistryInterface $setterRegistry,
        ServiceRegistryInterface $cleanerRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    )
    {
        $this->providerRegistry = $providerRegistry;
        $this->filterRegistry = $filterRegistry;
        $this->runnerRegistry = $runnerRegistry;
        $this->interpreterRegistry = $interpreterRegistry;
        $this->setterRegistry = $setterRegistry;
        $this->cleanerRegistry = $cleanerRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function doImport(DefinitionInterface $definition, $params)
    {
        $filter = null;

        if ($definition->getCreateVersion()) {
            Version::enable();
        } else {
            Version::disable();
        }

        $filterType = $definition->getFilter();
        if ($filterType) {
            $filter = $this->filterRegistry->get($filterType);
        }

        $data = $this->getData($definition, $params);

        if (\count($data) > 0) {
            $this->eventDispatcher->dispatch('import_definition.total', new ImportDefinitionEvent($definition, \count($data)));

            $this->runImport($definition, $params, $filter, $data);
        }

        $cleanerType = $definition->getCleaner();
        if ($cleanerType) {
            $cleaner = $this->cleanerRegistry->get($cleanerType);

            $this->logger->info(sprintf('Running Cleaner "%s"', $cleanerType));
            $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, sprintf('Running Cleaner "%s"', $cleanerType)));

            $cleaner->cleanup($definition, $this->objectIds);

            $this->logger->info(sprintf('Finished Cleaner "%s"', $cleanerType));
            $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, sprintf('Finished Cleaner "%s"', $cleanerType)));
        }

        if (\count($this->exceptions) > 0) {
            $this->sendDocument($definition, Document::getById($definition->getFailureNotificationDocument()));
        } else {
            $this->sendDocument($definition, Document::getById($definition->getSuccessNotificationDocument()));
        }

        $this->eventDispatcher->dispatch('import_definition.finished', new ImportDefinitionEvent($definition));
    }

    /**
     * @param DefinitionInterface $definition
     * @param $document
     * @throws \Exception
     */
    private function sendDocument(DefinitionInterface $definition, $document)
    {
        if ($document instanceof Document) {
            $params = [
                'exceptions' => $this->exceptions,
                'objectIds' => $this->objectIds,
                'className' => $definition->getClass(),
                'countObjects' => \count($this->objectIds),
                'countExceptions' => \count($this->exceptions),
                'name' => $definition->getName(),
                'provider' => $definition->getProvider()
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

    /**
     * @param DefinitionInterface $definition
     * @param $params
     * @return array
     */
    private function getData(DefinitionInterface $definition, $params)
    {
        /** @var ProviderInterface $provider */
        $provider = $this->providerRegistry->get($definition->getProvider());

        return $provider->getData($definition->getConfiguration(), $definition, $params);
    }

    /**
     * @param DefinitionInterface $definition
     * @param $params
     * @param null $filter
     * @param array $data
     * @throws \Exception
     */
    private function runImport(DefinitionInterface $definition, $params, $filter = null, array $data = [])
    {
        $count = 0;
        $countToClean = 1000;

        if (\is_array($data)) {
            foreach ($data as $row) {
                try {
                    $object = $this->importRow($definition, $row, $params, $filter);

                    if ($object instanceof Concrete) {
                        $this->objectIds[] = $object->getId();
                    }

                    if (($count + 1) % $countToClean === 0) {
                        \Pimcore::collectGarbage();
                        $this->logger->info('Clean Garbage');
                        $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, 'Collect Garbage'));
                    }

                    $count++;
                } catch (\Exception $ex) {
                    $this->logger->error($ex);

                    $this->exceptions[] = $ex;

                    $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, sprintf('Error: %s', $ex->getMessage())));

                    if ($definition->getStopOnException()) {
                        throw $ex;
                    }
                }

                $this->eventDispatcher->dispatch('import_definition.progress', new ImportDefinitionEvent($definition));
            }
        }
    }

    /**
     * @param DefinitionInterface $definition
     * @param $data
     * @param $params
     * @param null $filter
     * @return null|Concrete
     * @throws \Exception
     */
    private function importRow(DefinitionInterface $definition, $data, $params, $filter = null)
    {
        $runner = null;

        $object = $this->getObjectForPrimaryKey($definition, $data);

        if (null !== $object && !$object->getId()) {
            if ($definition->getSkipNewObjects()) {
                $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, 'Ignoring new Object'));
                return null;
            }
        } else {
            if ($definition->getSkipExistingObjects()) {
                $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, 'Ignoring existing Object'));
                return null;
            }
        }

        if ($filter instanceof FilterInterface && !$filter->filter($definition, $data, $object)) {
            $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, 'Filtered Object'));
            return null;
        }

        $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, sprintf('Import Object %s', ($object->getId() ? $object->getFullPath() : 'new'))));
        $this->eventDispatcher->dispatch('import_definition.object.start', new ImportDefinitionEvent($definition, $object));

        if ($definition->getRunner()) {
            $runner = $this->runnerRegistry->get($definition->getRunner());
        }


        if ($runner instanceof RunnerInterface) {
            $runner->preRun($object, $data, $definition, $params);
        }

        $this->logger->info(sprintf('Imported Object: %s', $object->getRealFullPath()));

        foreach ($definition->getMapping() as $mapItem) {
            $value = null;

            if (array_key_exists($mapItem->getFromColumn(), $data)) {
                $value = $data[$mapItem->getFromColumn()];
            }

            $this->setObjectValue($object, $mapItem, $value, $data, $definition, $params);
        }

        $object->setUserModification(0); //Set User to "system"

        if ($definition->getOmitMandatoryCheck()) {
            $object->setOmitMandatoryCheck(true);
        }

        $object->save();

        $this->eventDispatcher->dispatch('import_definition.status', new ImportDefinitionEvent($definition, sprintf('Imported Object %s', $object->getFullPath())));
        $this->eventDispatcher->dispatch('import_definition.object.finished', new ImportDefinitionEvent($definition, $object));

        if ($runner instanceof RunnerInterface) {
            $runner->postRun($object, $data, $definition, $params);
        }

        return $object;
    }

    /**
     * @param Concrete $object
     * @param Mapping $map
     * @param $value
     * @param $data
     * @param DefinitionInterface $definition
     * @param $params
     */
    private function setObjectValue(Concrete $object, Mapping $map, $value, $data, DefinitionInterface $definition, $params)
    {
        if ($map->getInterpreter()) {
            $interpreter = $this->interpreterRegistry->get($map->getInterpreter());
            $value = $interpreter->interpret($object, $value, $map, $data, $definition, $params, $map->getInterpreterConfig());
        }

        if ($map->getToColumn() === 'o_type' && $map->getSetter() !== 'object_type') {
            throw new \InvalidArgumentException('Type has to be used with ObjectType Setter!');
        }

        if ($map->getSetter()) {
            $setter = $this->setterRegistry->get($map->getSetter());
            $setter->set($object, $value, $map, $data);
        } else {
            $object->setValue($map->getToColumn(), $value);
        }
    }

    /**
     * @param DefinitionInterface $definition
     * @param $data
     * @return null|Concrete
     * @throws \Exception
     */
    private function getObjectForPrimaryKey(DefinitionInterface $definition, $data)
    {
        $class = $definition->getClass();
        $classDefinition = ClassDefinition::getByName($class);
        $obj = null;

        if (!$classDefinition instanceof ClassDefinition) {
            throw new \InvalidArgumentException(sprintf('Class not found %s', $class));
        }

        $classObject = '\Pimcore\Model\DataObject\\' . ucfirst($class);
        $classList = '\Pimcore\Model\DataObject\\' . ucfirst($class) . '\Listing';

        $list = new $classList();

        if ($list instanceof Listing) {
            $mapping = $definition->getMapping();
            $condition = [];
            $conditionValues = [];
            foreach ($mapping as $map) {
                if ($map->getPrimaryIdentifier()) {
                    $condition[] = '`' . $map->getToColumn() . '` = ?';
                    $conditionValues[] = $data[$map->getFromColumn()];
                }
            }

            if (\count($condition) === 0) {
                throw new \InvalidArgumentException('No primary identifier defined!');
            }

            $list->setUnpublished(true);
            $list->setCondition(implode(' AND ', $condition), $conditionValues);
            $list->setObjectTypes([Concrete::OBJECT_TYPE_VARIANT, Concrete::OBJECT_TYPE_OBJECT, Concrete::OBJECT_TYPE_FOLDER]);
            $list->load();
            $objectData = $list->getObjects();

            if (\count($objectData) === 1) {
                $obj = $objectData[0];
            }

            if (null === $obj) {
                $obj = new $classObject();
            }

            if ($obj instanceof AbstractObject) {
                $key = File::getValidFilename($this->createKey($definition, $data));

                if ($definition->getRelocateExistingObjects() || !$obj->getId()) {
                    $obj->setParent(Service::createFolderByPath($this->createPath($definition, $data)));
                }

                if ($definition->getRenameExistingObjects() || !$obj->getId()) {
                    if ($key && $definition->getKey()) {
                        $obj->setKey($key);
                    } else {
                        $obj->setKey(File::getValidFilename(implode('-', $conditionValues)));
                    }
                }

                if (!$obj->getKey()) {
                    throw new \InvalidArgumentException('No key set, please check your import-data');
                }

                $obj->setKey(Service::getUniqueKey($obj));

                return $obj;
            }

            if (\count($objectData) > 1) {
                throw new \InvalidArgumentException('Object with the same primary key was found multiple times');
            }
        }

        return null;
    }

    /**
     * @param DefinitionInterface $definition
     * @param $data
     * @return string
     */
    private function createPath(DefinitionInterface $definition, $data)
    {
        $placeholderHelper = new Placeholder();
        return $placeholderHelper->replacePlaceholders($definition->getObjectPath(), $data);
    }

    /**
     * @param DefinitionInterface $definition
     * @param $data
     * @return string
     */
    private function createKey(DefinitionInterface $definition, $data)
    {
        $placeholderHelper = new Placeholder();
        return $placeholderHelper->replacePlaceholders($definition->getKey(), $data);
    }
}