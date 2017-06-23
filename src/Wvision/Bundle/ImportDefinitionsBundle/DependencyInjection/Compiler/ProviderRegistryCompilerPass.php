<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

final class ProviderRegistryCompilerPass extends AbstractServiceRegistryCompilerPass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.provider',
            'import_definition.providers',
            'import_definition.provider'
        );
    }
}
