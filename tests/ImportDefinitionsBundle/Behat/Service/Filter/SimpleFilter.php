<?php

namespace ImportDefinitionsBundle\Behat\Service\Filter;

use ImportDefinitionsBundle\Filter\FilterInterface;
use ImportDefinitionsBundle\Model\DefinitionInterface;

class SimpleFilter implements FilterInterface
{
    public function filter(DefinitionInterface $definition, $data, $object)
    {
        return $data['doFilter'] !== '1';
    }
}