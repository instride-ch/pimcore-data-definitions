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
 * @copyright  Copyright (c) 2020 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Event;

use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

interface DefinitionEventInterface
{
    /**
     * @return ImportDefinitionInterface
     */
    public function getDefinition();

    /**
     * @return mixed
     */
    public function getSubject();
}
