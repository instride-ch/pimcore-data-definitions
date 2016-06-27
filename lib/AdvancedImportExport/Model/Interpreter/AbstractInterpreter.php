<?php

namespace AdvancedImportExport\Model\Interpreter;

/**
 * Class AbstractInterpreter
 * @package AdvancedImportExport\Model\Interpreter
 */
abstract class AbstractInterpreter{

    /**
     * @param $object
     * @param $value
     * @param $fromColumn
     * @param $toColumn
     * @return mixed
     */
    public static abstract function interpret($object, $value, $fromColumn, $toColumn);
}