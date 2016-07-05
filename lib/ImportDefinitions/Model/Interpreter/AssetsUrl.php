<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Object\Concrete;
use Pimcore\Placeholder;
use Pimcore\Tool;

/**
 * Class AssetsUrl
 * @package ImportDefinitions\Model\Interpreter
 */
class AssetsUrl extends AssetUrl {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $asset = parent::interpret($object, $value, $map, $data);

        if($asset) {
            return [$asset];
        }

        return null;
    }
}