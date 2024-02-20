<?php

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Persister;

use Pimcore\Model\DataObject\Concrete;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

interface PersisterInterface
{
    public function persist(Concrete $object, ImportDefinitionInterface $definition, array $params): void;
}
