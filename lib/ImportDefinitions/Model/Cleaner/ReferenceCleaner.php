<?php

namespace ImportDefinitions\Model\Cleaner;
use ImportDefinitions\Model\Log;
use Pimcore\Model\Dependency;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractCleaner
 * @package ImportDefinitions\Model\Cleaner
 */
class ReferenceCleaner extends AbstractCleaner {

    /**
     * @param Concrete[] $objects
     * @param Log[] $logs
     * @param Concrete[] $notFoundObjects
     * @return mixed
     */
    public function cleanup($objects, $logs, $notFoundObjects) {
        foreach($notFoundObjects as $obj)
        {
            $dependency = $obj->getDependencies();

            if($dependency instanceof Dependency) {
                if(count($dependency->getRequiredBy()) === 0) {
                    $obj->delete();
                }
                else {
                    $obj->setPublished(false);
                    $obj->save();
                }
            }
        }
    }
}