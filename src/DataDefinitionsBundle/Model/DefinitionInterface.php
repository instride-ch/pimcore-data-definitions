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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

if (interface_exists(DataDefinitionInterface::class)) {
    @trigger_error('Class Wvision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface is deprecated since version 2.1.0 and will be removed in 3.0.0. Use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class Wvision\Bundle\DataDefinitionsBundle\Model\Definition is deprecated since version 2.0.0 and will be removed in 3.0. Use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface interface instead.
     */
    interface DefinitionInterface
    {

    }
}

class_alias(DefinitionInterface::class, 'ImportDefinitionsBundle\Model\DefinitionInterface');
