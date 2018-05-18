<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\DependencyInjection\Compiler;

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
