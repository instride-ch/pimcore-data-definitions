<?php

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Service\Filter;

use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;

class SimpleFilter implements FilterInterface
{
    public function filter(DefinitionInterface $definition, $data, $object)
    {
        return $data['doFilter'] !== '1';
    }
}
