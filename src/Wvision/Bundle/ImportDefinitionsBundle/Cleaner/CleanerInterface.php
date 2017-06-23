<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Cleaner;

use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;

interface CleanerInterface
{
    /**
     *
     * @param DefinitionInterface $definition
     * @param int[] $objectIds
     * @return mixed
     */
    public function cleanup(DefinitionInterface $definition, $objectIds);
}