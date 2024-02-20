<?php

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Persister;

use Exception;
use Pimcore\Model\DataObject\Concrete;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

class Persister implements PersisterInterface
{
    /**
     * @throws Exception
     */
    public function persist(Concrete $object, ImportDefinitionInterface $definition, array $params): void
    {
        $object->save();
    }
}
