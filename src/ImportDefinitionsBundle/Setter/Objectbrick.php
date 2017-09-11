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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Setter;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use ImportDefinitionsBundle\Model\Mapping;

class Objectbrick implements SetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function set(Concrete $object, $value, Mapping $map, $data)
    {
        $keyParts = explode("~", $map->getToColumn());

        $config = $map->getSetterConfig();
        $fieldName = $config['brickField'];
        $class = $config['class'];
        $brickField = $keyParts[3];

        $brickGetter = "get" . ucfirst($fieldName);
        $brickSetter = "set" . ucfirst($fieldName);

        if (method_exists($object, $brickGetter)) {
            $brick = $object->$brickGetter();

            if (!$brick instanceof \Pimcore\Model\DataObject\Objectbrick) {
                $brick = new \Pimcore\Model\DataObject\Objectbrick($object, $fieldName);
                $object->$brickSetter($brick);
            }

            if ($brick instanceof \Pimcore\Model\DataObject\Objectbrick) {
                $brickClassGetter = "get" . ucfirst($class);
                $brickClassSetter = "set" . ucfirst($class);

                $brickFieldObject = $brick->$brickClassGetter();

                if (!$brickFieldObject instanceof AbstractData) {
                    $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\' . $class;

                    $brickFieldObject = new $brickFieldObjectClass($object);

                    $brick->$brickClassSetter($brickFieldObject);
                }

                $setter = "set" . ucfirst($brickField);

                if (method_exists($brickFieldObject, $setter)) {
                    $brickFieldObject->$setter($value);
                }
            }
        } else {
            //Brick does not exist?
        }
    }
}
