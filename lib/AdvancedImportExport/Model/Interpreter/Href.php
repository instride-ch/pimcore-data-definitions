<?php

namespace AdvancedImportExport\Model\Interpreter;
use AdvancedImportExport\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class Href
 * @package AdvancedImportExport\Model\Interpreter
 */
class Href extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map) {
        $config = $map->getConfig();
        $objectClass = $config['class'];

        $class = 'Pimcore\Model\Object\\' . $objectClass;

        if(Tool::classExists($class)) {
            $class = new $class();

            if($class instanceof Concrete) {
                $value = $class::getById($value);

                if($value instanceof Concrete) {
                    $object->setValue($map->getToColumn(), $value);
                }
            }
        }
    }
}