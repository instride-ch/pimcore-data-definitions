<?php

namespace ImportDefinitions\Model\Setter;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class Localizedfield
 * @package ImportDefinitions\Model\Setter
 */
class Localizedfield extends AbstractSetter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function set(Concrete $object, $value, Mapping $map, $data) {
        $config = $map->getSetterConfig();

        $setter = explode("~", $map->getToColumn());
        $setter = "set" . ucfirst($setter[0]);

        if(method_exists($object, $setter)) {
            $object->$setter($value, $config['language']);
        }
    }
}