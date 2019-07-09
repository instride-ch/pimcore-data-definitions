<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

final class InterpreterRegistryCompilerPass extends RegisterRegistryTypePass
{
    public const INTERPRETER_TAG = 'data_definitions.interpreter';

    public function __construct()
    {
        parent::__construct(
            'data_definitions.registry.interpreter',
            'data_definitions.form.registry.interpreter',
            'data_definitions.interpreters',
            self::INTERPRETER_TAG
        );
    }
}
