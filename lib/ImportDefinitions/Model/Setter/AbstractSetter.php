<?php

namespace ImportDefinitions\Model\Setter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractSetter
 * @package ImportDefinitions\Model\Setter
 */
abstract class AbstractSetter {
    /**
     * available Setter.
     *
     * @var array
     */
    public static $availableSetter = array('objectbrick', 'classificationstore', 'fieldcollection', 'localizedfield');

    /**
     * Add Setter.
     *
     * @param $setter
     */
    public static function addSetter($setter)
    {
        if (!in_array($setter, self::$availableSetter)) {
            self::$availableSetter[] = $setter;
        }
    }

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public abstract function set(Concrete $object, $value, Mapping $map, $data);
}