<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Importer;

use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;

interface ImporterInterface
{
    /**
     * @param DefinitionInterface $definition
     * @param $params
     * @return mixed
     */
    public function doImport(DefinitionInterface $definition, $params);
}