<?php

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Persister;

use Exception;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

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
