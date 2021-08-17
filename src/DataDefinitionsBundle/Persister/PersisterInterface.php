<?php

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Persister;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

interface PersisterInterface
{
    public function persist(Concrete $object, ImportDefinitionInterface $definition, array $params): void;
}
