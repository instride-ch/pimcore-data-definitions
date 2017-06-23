<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

final class InterpreterRegistryCompilerPass extends AbstractServiceRegistryCompilerPass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.interpreter',
            'import_definition.interpreters',
            'import_definition.interpreter'
        );
    }
}
