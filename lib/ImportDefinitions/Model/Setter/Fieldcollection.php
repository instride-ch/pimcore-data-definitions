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
use Pimcore\Model\Object\Fieldcollection\Data\AbstractData as AbstractFieldCollection;

/**
 * Class Fieldcollection
 * @package ImportDefinitions\Model\Setter
 */
class Fieldcollection extends AbstractSetter
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
        $keys = $config['fieldcollectionKeys'];
        $fieldName = $config['fieldcollectionField'];
        $class = $config['class'];
        $keys = explode(",", $keys);
        $fieldCollectionClass = 'Pimcore\Model\Object\Fieldcollection\Data\\' . ucfirst($class);
        $field = $keyParts[3];
        $mappedKeys = [];

        foreach ($keys as $key) {
            $tmp = explode(":", $key);

            $mappedKeys[] = [
                "from" => $tmp[0],
                "to" => $tmp[1]
            ];
        }

        $getter = "get" . ucfirst($fieldName);
        $setter = "set" . ucfirst($fieldName);

        if (method_exists($object, $getter)) {
            $fieldCollection = $object->$getter();

            if (!$fieldCollection instanceof \Pimcore\Model\Object\Fieldcollection) {
                $fieldCollection = new \Pimcore\Model\Object\Fieldcollection();
            }

            $items = $fieldCollection->getItems();
            $found = false;

            foreach ($items as $item) {
                if (is_a($item, $fieldCollectionClass)) {
                    if ($this->isValidKey($mappedKeys, $item, $data)) {
                        if ($item instanceof AbstractFieldCollection) {
                            $item->setValue($field, $value);
                        }

                        $found = true;
                    }
                }
            }

            if (!$found) {
                //Create new entry
                $item = new $fieldCollectionClass();

                if ($item instanceof AbstractFieldCollection) {
                    foreach ($mappedKeys as $key) {
                        $item->setValue($key['to'], $data[$key['from']]);
                    }

                    $item->setValue($field, $value);

                    $fieldCollection->add($item);
                }
            }

            $object->$setter($fieldCollection);
        }
    }

    /**
     * @param array $keys
     * @param $fieldcollection
     * @param $data
     *
     * @returns boolean
     */
    protected function isValidKey(array $keys, AbstractFieldCollection $fieldcollection, $data)
    {
        foreach ($keys as $key) {
            $getter = "get" . ucfirst($key['to']);

            if (method_exists($fieldcollection, $getter)) {
                $keyValue = $fieldcollection->$getter();

                if ($keyValue !== $data[$key['from']]) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }
}
