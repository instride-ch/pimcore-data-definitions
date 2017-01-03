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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions\Model\Setter;

use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractSetter
 * @package ImportDefinitions\Model\Setter
 */
abstract class AbstractSetter
{
    /**
     * available Setter.
     *
     * @var array
     */
    public static $availableSetter = array('objectbrick', 'classificationstore', 'fieldcollection', 'localizedfield', 'key', 'objectType');

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
    abstract public function set(Concrete $object, $value, Mapping $map, $data);
}
