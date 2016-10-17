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

namespace ImportDefinitions\Model\Cleaner;

use ImportDefinitions\Model\Definition;
use ImportDefinitions\Model\Log;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractCleaner
 * @package ImportDefinitions\Model\Cleaner
 */
abstract class AbstractCleaner
{

    /**
     * available cleaner.
     *
     * @var array
     */
    public static $availableCleaner = array('deleter', 'referenceCleaner', 'unpublisher', 'none');

    /**
     * Add cleaner.
     *
     * @param $cleaner
     */
    public static function addCleaner($cleaner)
    {
        if (!in_array($cleaner, self::$availableCleaner)) {
            self::$availableCleaner[] = $cleaner;
        }
    }

    /**
     *
     * @param Definition $definition
     * @param int[] $objectIds
     * @return mixed
     */
    abstract public function cleanup($definition, $objectIds);

    /**
     * @param Definition $definition
     * @param array $foundObjectIds
     *
     * @return Concrete[]
     */
    public function getObjectsToClean($definition, $foundObjectIds) {
        $logs = new Log\Listing();
        $logs->setCondition("definition = ?", array($definition->getId()));
        $logs = $logs->load();

        $notFound = [];

        foreach ($logs as $log) {
            $found = false;

            foreach ($foundObjectIds as $objectId) {
                if (intval($log->getO_Id()) === $objectId) {
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $notFoundObject = Concrete::getById($log->getO_Id());

                if ($notFoundObject instanceof Concrete) {
                    $notFound[] = $notFoundObject;
                }
            }
        }

        $this->deleteLogs($logs);
        $this->writeNewLogs($definition, $foundObjectIds);
        
        return $notFound;
    }

    /**
     * @param Log[] $logs
     */
    public function deleteLogs($logs) {
        //Delete Logs
        foreach ($logs as $log) {
            $log->delete();
        }
    }

    /**
     * @param Definition $definition
     * @param array $objectIds
     */
    public function writeNewLogs($definition, $objectIds) {
        //Save new Log
        foreach ($objectIds as $objId) {
            $log = new Log();
            $log->setO_Id($objId);
            $log->setDefinition($definition->getId());
            $log->save();
        }
    }
}
