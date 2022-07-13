<?php

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Service\Filter;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

class SimpleFilter implements FilterInterface
{
    public function filter(DataDefinitionInterface $definition, array $data, Concrete $object, array $params): bool
    {
        return $data['doFilter'] !== '1';
    }
}
