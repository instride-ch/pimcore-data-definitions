<?php

namespace AdvancedImportExport\Model\Interpreter;

/**
 * Class AbstractInterpreter
 * @package AdvancedImportExport\Model\Interpreter
 */
abstract class AbstractInterpreter{
    /**
     * available Interpreter.
     *
     * @var array
     */
    public static $availableInterpreter = array('objectbrick', 'classificationstore');

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
     * @param $object
     * @param $value
     * @param $fromColumn
     * @param $toColumn
     * @return mixed
     */
    public static function interpret($object, $value, $fromColumn, $toColumn) {}
}