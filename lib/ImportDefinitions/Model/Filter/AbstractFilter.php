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

namespace ImportDefinitions\Model\Filter;

use ImportDefinitions\Model\Definition;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractFilter
 * @package ImportDefinitions\Model\Filter
 */
abstract class AbstractFilter
{

    /**
     * available filter.
     *
     * @var array
     */
    public static $availableFilter = array();

    /**
     * Add filter.
     *
     * @param $filter
     */
    public static function addFilter($filter)
    {
        if (!in_array($filter, self::$availableFilter)) {
            self::$availableFilter[] = $filter;
        }
    }

    /**
     * @param Definition $definition
     * @param array $data
     * @param Concrete $object
     *
     * @return boolean
     */
    abstract public function filter($definition, $data, $object);
}