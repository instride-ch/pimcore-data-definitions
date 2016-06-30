<?php

namespace ImportDefinitions\Model\Cleaner;
use ImportDefinitions\Model\Log;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractCleaner
 * @package ImportDefinitions\Model\Cleaner
 */
class Deleter extends AbstractCleaner {

    /**
     * @param Concrete[] $objects
     * @param Log[] $logs
     * @param Concrete[] $notFoundObjects
     * @return mixed
     */
    public function cleanup($objects, $logs, $notFoundObjects) {
        foreach($notFoundObjects as $obj)
        {
            $obj->delete();
        }
    }
}