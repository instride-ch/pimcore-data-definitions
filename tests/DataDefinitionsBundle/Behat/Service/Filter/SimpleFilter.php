<?php

namespace WVision\Bundle\DataDefinitionsBundle\Behat\Service\Filter;

use WVision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use WVision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;

class SimpleFilter implements FilterInterface
{
    public function filter(DefinitionInterface $definition, $data, $object)
    {
        return $data['doFilter'] !== '1';
    }
}
