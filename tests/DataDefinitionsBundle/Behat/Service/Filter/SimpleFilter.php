<?php

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Service\Filter;

use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

class SimpleFilter implements FilterInterface
{
    public function filter(DataDefinitionInterface $definition, $data, $object)
    {
        return $data['doFilter'] !== '1';
    }
}
