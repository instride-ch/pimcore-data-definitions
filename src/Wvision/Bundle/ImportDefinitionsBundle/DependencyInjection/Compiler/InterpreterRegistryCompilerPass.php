<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

final class InterpreterRegistryCompilerPass extends RegisterRegistryTypePass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.interpreter',
            'import_definition.form.registry.interpreter',
            'import_definition.interpreters',
            'import_definition.interpreter'
        );
    }
}
