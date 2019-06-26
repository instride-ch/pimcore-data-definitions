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

namespace ImportDefinitionsBundle;

use WVision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

if (class_exists(DefinitionRepository::class)) {
    @trigger_error('Class ImportDefinitionsBundle\Repository is deprecated since version 2.3.0 and will be removed in 3.0.0. Use WVision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository class instead.',
        E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class ImportDefinitionsBundle\Repository is deprecated since version 2.3.0 and will be removed in 3.0.0. Use WVision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository class instead.
     */
    class Repository
    {
    }
}


