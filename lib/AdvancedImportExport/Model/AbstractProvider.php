<?php

namespace AdvancedImportExport\Model;

use AdvancedImportExport\Model\Interpreter\Classificationstore;
use AdvancedImportExport\Model\Interpreter\Objectbrick;
use AdvancedImportExport\Model\Mapping\FromColumn;
use Pimcore\File;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\Listing;
use Pimcore\Model\Object\Service;

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
     * @return mixed
     */
    public abstract function runImport($definition, $params);

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
     * @param $fromColumn
     * @param $toColumn
     * @param $value
     */
    public function setObjectValue(Concrete $object, $fromColumn, $toColumn, $value) {
        $keyParts = explode("~", $toColumn);

        if(count($keyParts) > 1) {
            $type = $keyParts[0];

            if($type === 'objectbrick') {
                Objectbrick::interpret($object, $value, $fromColumn, $toColumn);
            }
            else if($type === 'classificationstore') {
               Classificationstore::interpret($object, $value, $fromColumn, $toColumn);
            }
        }
        else {
            $object->setValue($toColumn, $value);
        }
    }
}