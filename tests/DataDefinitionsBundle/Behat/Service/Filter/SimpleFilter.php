<?php

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Service\Filter;

use Pimcore\Model\DataObject\Concrete;
use Instride\Bundle\DataDefinitionsBundle\Context\FilterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

class SimpleFilter implements FilterInterface
{
    public function filter(FilterContextInterface $context): bool
    {
        return $context->getDataRow()['doFilter'] !== '1';
    }
}
