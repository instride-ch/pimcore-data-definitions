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

namespace Wvision\Bundle\DataDefinitionsBundle\Setter;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class ObjectbrickSetter implements SetterInterface, GetterInterface
{
    public function set(Concrete $object, $value, ImportMapping $map, $data)
    {
        $keyParts = explode('~', $map->getToColumn());

        $config = $map->getSetterConfig();
        $fieldName = $config['brickField'];
        $class = $config['class'];
        $brickField = $keyParts[3];

        $brickGetter = sprintf('get%s', ucfirst($fieldName));
        $brickSetter = sprintf('set%s', ucfirst($fieldName));

        if (method_exists($object, $brickGetter)) {
            $brick = $object->$brickGetter();

            if (!$brick instanceof \Pimcore\Model\DataObject\Objectbrick) {
                $brick = new \Pimcore\Model\DataObject\Objectbrick($object, $fieldName);
                $object->$brickSetter($brick);
            }

            if ($brick instanceof \Pimcore\Model\DataObject\Objectbrick) {
                $brickClassGetter = sprintf('get%s', ucfirst($class));
                $brickClassSetter = sprintf('set%s', ucfirst($class));

                $brickFieldObject = $brick->$brickClassGetter();

                if (!$brickFieldObject instanceof AbstractData) {
                    $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\'.$class;

                    $brickFieldObject = new $brickFieldObjectClass($object);

                    $brick->$brickClassSetter($brickFieldObject);
                }

                $setter = sprintf('set%s', ucfirst($brickField));

                if (method_exists($brickFieldObject, $setter)) {
                    $brickFieldObject->$setter($value);
                }
            }
        }
    }

    public function get(Concrete $object, ExportMapping $map, $data)
    {
        $keyParts = explode('~', $map->getFromColumn());

        $config = $map->getGetterConfig();
        $fieldName = $config['brickField'];
        $class = $config['class'];
        $brickField = $keyParts[3];

        $brickGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($object, $brickGetter)) {
            $brick = $object->$brickGetter();

            if (!$brick instanceof \Pimcore\Model\DataObject\Objectbrick) {
                return;
            }

            if ($brick instanceof \Pimcore\Model\DataObject\Objectbrick) {
                $brickClassGetter = sprintf('get%s', ucfirst($class));
                $brickClassSetter = sprintf('set%s', ucfirst($class));

                $brickFieldObject = $brick->$brickClassGetter();

                if (!$brickFieldObject instanceof AbstractData) {
                    $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\'.$class;

                    $brickFieldObject = new $brickFieldObjectClass($object);

                    $brick->$brickClassSetter($brickFieldObject);
                }

                $getter = sprintf('get%s', ucfirst($brickField));

                if (method_exists($brickFieldObject, $getter)) {
                    return $brickFieldObject->$getter();
                }
            }
        }

        return null;
    }
}


