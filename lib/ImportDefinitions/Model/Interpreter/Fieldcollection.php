<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

use Pimcore\Model\Object\Fieldcollection\Data\AbstractData as AbstractFieldCollection;

/**
 * Class Fieldcollection
 * @package ImportDefinitions\Model\Interpreter
 */
class Fieldcollection extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $keyParts = explode("~", $map->getToColumn());

        $config = $map->getConfig();
        $keys = $config['fieldcollectionKeys'];
        $fieldName = $config['fieldcollectionField'];
        $class = $config['class'];
        $keys = explode(",", $keys);
        $fieldCollectionClass = 'Pimcore\Model\Object\Fieldcollection\Data\\' . $class;
        $field = $keyParts[3];

        foreach($keys as &$key) {
            $tmp = explode(":", $key);

            $key = [
                "from" => $tmp[0],
                "to" => $tmp[1]
            ];
        }

        $getter = "get" . ucfirst($fieldName);
        $setter = "set" . ucfirst($fieldName);

        if(method_exists($object, $getter)) {
            $fieldCollection = $object->$getter();

            if(!$fieldCollection instanceof \Pimcore\Model\Object\Fieldcollection) {
                $fieldCollection = new \Pimcore\Model\Object\Fieldcollection();
            }

            $items = $fieldCollection->getItems();
            $found = false;

            foreach($items as $item) {
                if(is_a($item, $fieldCollectionClass)) {
                    if($this->isValidKey($keys, $item, $data)) {
                        if($item instanceof AbstractFieldCollection) {
                            $item->setValue($field, $value);
                        }

                        $found = true;
                    }
                }
            }

            if(!$found) {
                //Create new entry
                $item = new $fieldCollectionClass();

                if($item instanceof AbstractFieldCollection) {
                    foreach($keys as $key) {
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
    protected function isValidKey(array $keys, AbstractFieldCollection $fieldcollection, $data) {
        foreach($keys as $key) {
            $getter = "get" . ucfirst($key['to']);

            if(method_exists($fieldcollection, $getter)) {
                $keyValue = $fieldcollection->$getter();

                if($keyValue !== $data[$key['from']]) {
                    return false;
                }
            }
            else {
                return false;
            }
        }

        return true;
    }
}