<?php

namespace AdvancedImportExport\Model\Interpreter;
use AdvancedImportExport\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class Localizedfield
 * @package AdvancedImportExport\Model\Interpreter
 */
class Localizedfield extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $config = $map->getConfig();

        $setter = explode("~", $map->getToColumn());
        $setter = "set" . ucfirst($setter[0]);

        if(method_exists($object, $setter)) {
            $object->$setter($value, $config['language']);
        }
    }
}