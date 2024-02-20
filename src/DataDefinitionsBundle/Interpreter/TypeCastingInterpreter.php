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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use InvalidArgumentException;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class TypeCastingInterpreter implements InterpreterInterface
{
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_STRING = 'string';
    public const TYPE_BOOLEAN = 'boolean';

    public function interpret(InterpreterContextInterface $context): mixed
    {
        $type = $context->getConfiguration()['toType'];

        switch ($type) {
            case static::TYPE_INT:
                return (int)$context->getValue();
            case static::TYPE_FLOAT:
                return (float)$context->getValue();
            case static::TYPE_STRING:
                return (string)$context->getValue();
            case static::TYPE_BOOLEAN:
                return (boolean)$context->getValue();
        }

        throw new InvalidArgumentException(sprintf('Not valid type cast given, given %s', $type));
    }
}
