<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

final class ProviderRegistryCompilerPass extends RegisterRegistryTypePass
{
    public function __construct()
    {
        parent::__construct(
            'import_definition.registry.provider',
            'import_definition.form.registry.provider',
            'import_definition.providers',
            'import_definition.provider'
        );
    }
}
