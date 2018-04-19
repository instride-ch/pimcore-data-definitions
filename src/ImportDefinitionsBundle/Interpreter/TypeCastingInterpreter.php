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

namespace ImportDefinitionsBundle\Interpreter;

use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;

class TypeCastingInterpreter implements InterpreterInterface
{
    const TYPE_INT = 'int';

    const TYPE_STRING = 'string';

    const TYPE_BOOLEAN = 'boolean';

    /**
     * {@inheritdoc}
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data, DefinitionInterface $definition, $params, $configuration)
    {
        $type = $configuration['toType'];

        switch($type) {
            case static::TYPE_INT:
                return (int) $value;
                break;
            case static::TYPE_STRING:
                return (string) $value;
                break;
            case static::TYPE_BOOLEAN:
                return (boolean) $value;
                break;
        }

        throw new \InvalidArgumentException(sprintf('not valid type cast given, given %s', $type));
    }
}
