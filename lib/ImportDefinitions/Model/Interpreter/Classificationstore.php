<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class Classificationstore
 * @package ImportDefinitions\Model\Interpreter
 */
class Classificationstore extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $mapConfig = $map->getConfig();
        $fieldName = $mapConfig['classificationstoreField'];
        $keyConfig = intval($mapConfig['keyId']);
        $groupConfig = intval($mapConfig['groupId']);

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