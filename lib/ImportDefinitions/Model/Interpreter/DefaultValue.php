<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class DefaultValue
 * @package ImportDefinitions\Model\Interpreter
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
        $config = $map->getInterpreterConfig();
        $value = $config['value'];
        
        return $value;
    }
}