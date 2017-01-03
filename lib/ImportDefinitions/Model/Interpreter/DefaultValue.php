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

namespace ImportDefinitions\Model\Interpreter;

use ImportDefinitions\Model\Definition;
use ImportDefinitions\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class DefaultValue
 * @package ImportDefinitions\Model\Interpreter
 */
class DefaultValue extends AbstractInterpreter
{

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @param Definition $definition
     * @param array $params
     *
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data, Definition $definition, $params)
    {
        $config = $map->getInterpreterConfig();
        $value = $config['value'];
        
        return $value;
    }
}
