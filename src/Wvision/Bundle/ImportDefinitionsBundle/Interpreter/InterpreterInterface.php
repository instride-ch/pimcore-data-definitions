<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Interpreter;

use Pimcore\Model\Object\Concrete;
use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping;

interface InterpreterInterface
{
    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @param DefinitionInterface $definition
     * @param array $params
     *
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data, DefinitionInterface $definition, $params);
}