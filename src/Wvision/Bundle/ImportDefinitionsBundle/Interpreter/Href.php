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

namespace Wvision\Bundle\ImportDefinitionsBundle\Interpreter;

use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

class Href implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data, DefinitionInterface $definition, $params)
    {
        $config = $map->getInterpreterConfig();
        $objectClass = $config['class'];

        $class = 'Pimcore\Model\Object\\' . $objectClass;

        if (Tool::classExists($class)) {
            $class = new $class();

            if ($class instanceof Concrete) {
                $ret = $class::getById($value);

                if ($ret instanceof Concrete) {
                    return $ret;
                }
            }
        }

        return $value;
    }
}
