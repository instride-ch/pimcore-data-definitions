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
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Form\Type\Interpreter;

use Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter\DefinitionType as NewDefinitionType;

if (class_exists(NewDefinitionType::class)) {
    @trigger_error('Class ImportDefinitionsBundle\Form\Type\Interpreter\DefinitionType is deprecated since version 2.3.0 and will be removed in 3.0.0. Use Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter\DefinitionType class instead.',
        E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class ImportDefinitionsBundle\Form\Type\Interpreter\DefinitionType is deprecated since version 2.3.0 and will be removed in 3.0.0. Use Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter\DefinitionType class instead.
     */
    final class DefinitionType
    {
    }
}
