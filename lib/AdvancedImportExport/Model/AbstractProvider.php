<?php

namespace AdvancedImportExport\Model;

use AdvancedImportExport\Model\Cleaner\AbstractCleaner;
use AdvancedImportExport\Model\Interpreter\AbstractInterpreter;
use AdvancedImportExport\Model\Mapping\FromColumn;
use Pimcore\File;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\Listing;
use Pimcore\Model\Object\Service;
use Pimcore\Tool;

/**
 * Base Class every Provider needs to implement
 *
 * Class AbstractProvider
 * @package AdvancedImportExport
 */
abstract class AbstractProvider {
    /**
     * available Providers.
     *
     * @var array
     */
    public static $availableProviders = array('csv', 'sql');

    /**
     * @var Mapping[]
     */
    public $mappings;

    /**
     * Add Provider.
     *
     * @param $provider
     */
    public static function addProvider($provider)
    {
        if (!in_array($provider, self::$availableProviders)) {
            self::$availableProviders[] = $provider;
        }
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            $setter = 'set'.ucfirst($key);

            if($key === "mappings") {
                $mappings = [];

                if(is_array($value)) {
                    foreach($value as $vMap) {
                        $mapping = new Mapping();
                        $mapping->setValues($vMap);

                        $mappings[] = $mapping;
                    }

                    $value = $mappings;
                }
            }

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return array
     */
    public static function getAvailableProviders()
    {
        return self::$availableProviders;
    }

    /**
     * @param array $availableProviders
     */
    public static function setAvailableProviders($availableProviders)
    {
        self::$availableProviders = $availableProviders;
    }

    /**
     * @return Mapping[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param Mapping[] $mappings
     */
    public function setMappings($mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public abstract function getColumns();

    /**
     * @param Definition $definition
     * @param $params
     * @return Concrete[]
     */
    protected abstract function runImport($definition, $params);

    /**
     * @param Definition $definition
     * @param $params
     */
    public function doImport($definition, $params) {
        $logs = new Log\Listing();
        $logs->setCondition("definition = ?", array($definition->getId()));
        $logs = $logs->getData();

        $objects = $this->runImport($definition, $params);

        //Compare with logs and cleanup
        $notFound = [];

        foreach($logs as $log) {
            $found = false;

            foreach($objects as $object) {
                if(intval($log->getO_Id()) === $object->getId()) {
                    $found = true;

                    break;
                }
            }

            if(!$found) {
                $notFoundObject = Concrete::getById($log->getO_Id());

                if($notFoundObject instanceof Concrete) {
                    $notFound[] = $notFoundObject;
                }
            }
        }

        //Get Cleanup type
        $type = $definition->getCleaner();
        $class = 'AdvancedImportExport\\Model\\Cleaner\\' . ucfirst($type);
        
        if(Tool::classExists($class)) {
            $class = new $class();
            
            if($class instanceof AbstractCleaner) {
                $class->cleanup($objects, $logs, $notFound);
            }
        }

        //Delete Logs
        foreach($logs as $log) {
            $log->delete();
        }

        //Save new Log
        foreach($objects as $obj) {
            $log = new Log();
            $log->setO_Id($obj->getId());
            $log->setDefinition($definition->getId());
            $log->save();
        }
    }

    /**
     * @param Definition $definition
     * @param array $data
     *
     * @throws \Exception
     *
     * @return Concrete
     */
    public function getObjectForPrimaryKey($definition, $data) {
        $class = $definition->getClass();
        $classDefinition =ClassDefinition::getByName($class);

        if(!$classDefinition instanceof ClassDefinition) {
            throw new \Exception("Class not found $class");
        }

        $classObject = '\Pimcore\Model\Object\\' . $class;
        $classList = '\Pimcore\Model\Object\\' . $class . '\Listing';

        $list = new $classList();

        if($list instanceof Listing) {
            $mapping = $definition->getMapping();
            $condition = [];
            $conditionValues = [];
            foreach($mapping as $map) {
                if($map->getPrimaryIdentifier()) {
                    $condition[] = $map->getToColumn() . " = ?";
                    $conditionValues[] = $data[$map->getFromColumn()];
                }
            }

            if(count($condition) === 0)
            {
                throw new \Exception("No primary identifier defined!");
            }

            $list->setUnpublished(true);
            $list->setCondition(implode(" AND ", $condition), $conditionValues);
            $objectData = $list->load();

            if(count($objectData) === 1) {
                return $objectData[0];
            }

            if(count($objectData) === 0) {
                $obj = new $classObject();

                if($obj instanceof AbstractObject) {
                    $obj->setKey(File::getValidFilename(implode("-", $conditionValues)));
                    $obj->setParent(Service::createFolderByPath($definition->createPath($data)));
                }

                return $obj;
            }

            if(count($objectData) > 1) {
                throw new \Exception("Object with the same primary key was fount multiple times");
            }
        }

        return null;
    }

    /**
     * @param Concrete $object
     * @param Mapping $map
     * @param $value
     * @param array $data
     */
    public function setObjectValue(Concrete $object, Mapping $map, $value, $data) {
        $mapConfig = $map->getConfig();

        if($mapConfig['interpreter']) {
            $class = 'AdvancedImportExport\Model\Interpreter\\' . ucfirst($mapConfig['interpreter']);

            if(Tool::classExists($class)) {
                $class = new $class();

                if($class instanceof AbstractInterpreter) {
                    $class->interpret($object, $value, $map, $data);
                }
            }
        }
        else {
            $object->setValue($map->getToColumn(), $value);
        }
    }
}