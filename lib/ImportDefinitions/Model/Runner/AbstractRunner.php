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

namespace ImportDefinitions\Model\Runner;

use ImportDefinitions\Model\Definition;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractRunner
 * @package ImportDefinitions\Model\Runner
 */
abstract class AbstractRunner
{
    /**
     * available Interpreter.
     *
     * @var array
     */
    public static $availableRunner = array();

    /**
     * Add Runner.
     *
     * @param $runner
     */
    public static function addRunner($runner)
    {
        if (!in_array($runner, self::$availableRunner)) {
            self::$availableRunner[] = $runner;
        }
    }

    /**
     * @param Concrete $object
     * @param array $data
     * @param Definition $definition
     * @param array $params
     */
    public function preRun(Concrete $object, $data, Definition $definition, $params) {

    }

    /**
     * @param Concrete $object
     * @param array $data
     * @param Definition $definition
     * @param array $params
     */
    public function postRun(Concrete $object, $data, Definition $definition, $params) {

    }
}
