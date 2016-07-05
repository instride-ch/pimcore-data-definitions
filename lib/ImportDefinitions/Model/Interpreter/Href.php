<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class Href
 * @package ImportDefinitions\Model\Interpreter
 */
class Href extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $config = $map->getInterpreterConfig();
        $objectClass = $config['class'];

        $class = 'Pimcore\Model\Object\\' . $objectClass;

        if(Tool::classExists($class)) {
            $class = new $class();

            if($class instanceof Concrete) {
                $ret = $class::getById($value);

                if($ret instanceof Concrete) {
                    return $ret;
                }
            }
        }

        return $value;
    }
}