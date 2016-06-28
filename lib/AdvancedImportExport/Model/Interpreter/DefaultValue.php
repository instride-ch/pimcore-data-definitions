<?php

namespace AdvancedImportExport\Model\Interpreter;
use AdvancedImportExport\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class DefaultValue
 * @package AdvancedImportExport\Model\Interpreter
 */
class DefaultValue extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $config = $map->getConfig();
        $value = $config['value'];
        
        $object->setValue($map->getToColumn(), $value);
    }
}