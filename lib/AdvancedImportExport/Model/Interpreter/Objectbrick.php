<?php

namespace AdvancedImportExport\Model\Interpreter;
use AdvancedImportExport\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\Objectbrick\Data\AbstractData;

/**
 * Class Objectbrick
 * @package AdvancedImportExport\Model\Interpreter
 */
class Objectbrick extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map) {
        $keyParts = explode("~", $map->getToColumn());

        $config = $map->getConfig();
        $fieldName = $config['brickField'];
        $class = $keyParts[2];
        $brickField = $keyParts[3];

        $brickGetter = "get" . ucfirst($fieldName);
        $brickSetter = "set" . ucfirst($fieldName);

        if (method_exists($object, $brickGetter)) {
            $brick = $object->$brickGetter();

            if (!$brick instanceof \Pimcore\Model\Object\Objectbrick) {
                $brick = new \Pimcore\Model\Object\Objectbrick($object, $fieldName);
                $object->$brickSetter($brick);
            }

            if ($brick instanceof Objectbrick) {
                $brickClassGetter = "get" . $class;
                $brickClassSetter = "set" . $class;

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