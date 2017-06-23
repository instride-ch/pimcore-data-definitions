<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Filter;

use Pimcore\Model\Object\Concrete;
use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;

interface FilterInterface
{
    /**
     * @param DefinitionInterface $definition
     * @param array $data
     * @param Concrete $object
     *
     * @return boolean
     */
    public function filter(DefinitionInterface $definition, $data, $object);
}