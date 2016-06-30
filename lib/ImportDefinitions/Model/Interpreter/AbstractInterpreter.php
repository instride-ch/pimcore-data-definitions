<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractInterpreter
 * @package ImportDefinitions\Model\Interpreter
 */
abstract class AbstractInterpreter{
    /**
     * available Interpreter.
     *
     * @var array
     */
    public static $availableInterpreter = array('objectbrick', 'classificationstore', 'href', 'multiHref', 'defaultValue');

    /**
     * Add Interpreter.
     *
     * @param $interpreter
     */
    public static function addInterpreter($interpreter)
    {
        if (!in_array($interpreter, self::$availableInterpreter)) {
            self::$availableInterpreter[] = $interpreter;
        }
    }

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public abstract function interpret(Concrete $object, $value, Mapping $map, $data);
}