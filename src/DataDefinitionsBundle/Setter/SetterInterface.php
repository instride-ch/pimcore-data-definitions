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

namespace Wvision\Bundle\DataDefinitionsBundle\Setter;

use Wvision\Bundle\DataDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;

interface SetterInterface
{
    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     */
    public function set(Concrete $object, $value, Mapping $map, $data);
}

class_alias(SetterInterface::class, 'ImportDefinitionsBundle\Setter\SetterInterface');
