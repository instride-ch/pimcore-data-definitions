<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

final class SetterRegistryCompilerPass extends AbstractServiceRegistryCompilerPass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.setter',
            'import_definition.setters',
            'import_definition.setter'
        );
    }
}
