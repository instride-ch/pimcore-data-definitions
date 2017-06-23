<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

final class FilterRegistryCompilerPass extends AbstractServiceRegistryCompilerPass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.filter',
            'import_definition.filters',
            'import_definition.filter'
        );
    }
}
