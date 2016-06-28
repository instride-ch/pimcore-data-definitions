<?php

namespace AdvancedImportExport\Model\Cleaner;
use AdvancedImportExport\Model\Log;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractCleaner
 * @package AdvancedImportExport\Model\Cleaner
 */
class Unpublisher extends AbstractCleaner {

    /**
     * @param Concrete[] $objects
     * @param Log[] $logs
     * @param Concrete[] $notFoundObjects
     * @return mixed
     */
    public function cleanup($objects, $logs, $notFoundObjects) {
        foreach($notFoundObjects as $obj)
        {
            $obj->setPublished(false);
            $obj->save();
        }
    }
}