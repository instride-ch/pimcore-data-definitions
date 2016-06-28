<?php

namespace AdvancedImportExport\Model\Interpreter;
use AdvancedImportExport\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class Classificationstore
 * @package AdvancedImportExport\Model\Interpreter
 */
class Classificationstore extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map) {
        $keyParts = explode("~", $map->getToColumn());

        $mapConfig = $map->getConfig();
        $fieldName = $mapConfig['classificationstoreField'];
        $keyConfig = $keyParts[2];
        $groupConfig = $keyParts[3];

        $classificationStoreGetter = "get" . ucfirst($fieldName);

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if($classificationStore instanceof \Pimcore\Model\Object\Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if(!$groups[$groupConfig]) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                $classificationStore->setLocalizedKeyValue($groupConfig, $keyConfig, $value);
            }

        }
    }
}