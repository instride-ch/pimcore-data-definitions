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

namespace ImportDefinitionsBundle\Model;

if (interface_exists(DataDefinitionInterface::class)) {
    @trigger_error('Class ImportDefinitionsBundle\Model\DefinitionInterface is deprecated since version 2.1.0 and will be removed in 3.0.0. Use ImportDefinitionsBundle\Model\DataDefinitionInterface class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class ImportDefinitionsBundle\Model\Definition is deprecated since version 2.0.0 and will be removed in 3.0. Use ImportDefinitionsBundle\Model\DataDefinitionInterface interface instead.
     */
    interface DefinitionInterface
    {

    }
}