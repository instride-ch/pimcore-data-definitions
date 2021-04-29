<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use InvalidArgumentException;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareTrait;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class TypeCastingInterpreter implements InterpreterInterface, DataSetAwareInterface
{
    use DataSetAwareTrait;

    protected const TYPE_INT = 'int';
    protected const TYPE_STRING = 'string';
    protected const TYPE_BOOLEAN = 'boolean';

    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        $data,
        DataDefinitionInterface $definition,
        $params,
        $configuration
    ) {
        $type = $configuration['toType'];

        switch ($type) {
            case static::TYPE_INT:
                return (int)$value;
            case static::TYPE_STRING:
                return (string)$value;
            case static::TYPE_BOOLEAN:
                return (boolean)$value;
        }

        throw new InvalidArgumentException(sprintf('Not valid type cast given, given %s', $type));
    }
}
