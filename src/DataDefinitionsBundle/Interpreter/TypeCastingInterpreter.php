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
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class TypeCastingInterpreter implements InterpreterInterface
{
    protected const TYPE_INT = 'int';
    protected const TYPE_STRING = 'string';
    protected const TYPE_BOOLEAN = 'boolean';

    public function interpret(InterpreterContextInterface $context): mixed
    {
        $type = $context->getConfiguration()['toType'];

        switch ($type) {
            case static::TYPE_INT:
                return (int)$context->getValue();
            case static::TYPE_STRING:
                return (string)$context->getValue();
            case static::TYPE_BOOLEAN:
                return (boolean)$context->getValue();
        }

        throw new InvalidArgumentException(sprintf('Not valid type cast given, given %s', $type));
    }
}
