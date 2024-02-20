<?php

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class PersisterRegistryCompilerPass extends RegisterSimpleRegistryTypePass
{
    public const PERSISTER_TAG = 'data_definitions.persister';

    public function __construct()
    {
        parent::__construct(
            'data_definitions.registry.persister',
            'data_definitions.persisters',
            self::PERSISTER_TAG
        );
    }
}
