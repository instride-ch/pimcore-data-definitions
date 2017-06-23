<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Setter;

use Pimcore\Model\Object\Concrete;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping;

interface SetterInterface
{
    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function set(Concrete $object, $value, Mapping $map, $data);
}