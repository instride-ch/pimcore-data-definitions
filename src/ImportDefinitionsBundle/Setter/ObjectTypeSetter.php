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

namespace ImportDefinitionsBundle\Setter;

use Pimcore\Model\DataObject\Concrete;
use ImportDefinitionsBundle\Model\Mapping;

class ObjectTypeSetter implements SetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function set(Concrete $object, $value, Mapping $map, $data)
    {
        if ($value === Concrete::OBJECT_TYPE_FOLDER) {
            $object->setType(Concrete::OBJECT_TYPE_FOLDER);
            return;
        }

        if ($value === Concrete::OBJECT_TYPE_VARIANT) {
            $object->setType(Concrete::OBJECT_TYPE_VARIANT);
            return;
        }

        $object->setType(Concrete::OBJECT_TYPE_OBJECT);
    }
}
