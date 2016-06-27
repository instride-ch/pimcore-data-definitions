<?php

namespace AdvancedImportExport\Model\Interpreter;

/**
 * Class Classificationstore
 * @package AdvancedImportExport\Model\Interpreter
 */
class Classificationstore extends AbstractInterpreter {

    /**
     * @param $object
     * @param $value
     * @param $fromColumn
     * @param $toColumn
     * @return mixed
     */
    public static function interpret($object, $value, $fromColumn, $toColumn) {
        $keyParts = explode("~", $toColumn);

        $fieldName = $keyParts[1];
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