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
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions\Model\Setter;

use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\Objectbrick\Data\AbstractData;

/**
 * Class Objectbrick
 * @package ImportDefinitions\Model\Setter
 */
class Objectbrick extends AbstractSetter
{

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
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

            if (!$brick instanceof \Pimcore\Model\Object\Objectbrick) {
                $brick = new \Pimcore\Model\Object\Objectbrick($object, $fieldName);
                $object->$brickSetter($brick);
            }

            if ($brick instanceof \Pimcore\Model\Object\Objectbrick) {
                $brickClassGetter = "get" . ucfirst($class);
                $brickClassSetter = "set" . ucfirst($class);

                $brickFieldObject = $brick->$brickClassGetter();

                if (!$brickFieldObject instanceof AbstractData) {
                    $brickFieldObjectClass = 'Pimcore\Model\Object\Objectbrick\Data\\' . $class;

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
