<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

final class SetterRegistryCompilerPass extends RegisterRegistryTypePass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.setter',
            'import_definition.form.registry.setter',
            'import_definition.setters',
            'import_definition.setter'
        );
    }
}
