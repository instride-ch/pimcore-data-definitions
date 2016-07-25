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
    public static $availableCleaner = array('deleter', 'referenceCleaner', 'unpublisher');

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
     * @param Concrete[] $objects
     * @param Log[] $logs
     * @param Concrete[] $notFoundObjects
     * @return mixed
     */
    abstract public function cleanup($objects, $logs, $notFoundObjects);
}
