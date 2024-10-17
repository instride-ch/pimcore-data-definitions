<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use InvalidArgumentException;

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
                return (int) $context->getValue();
            case static::TYPE_FLOAT:
                return (float) $context->getValue();
            case static::TYPE_STRING:
                return (string) $context->getValue();
            case static::TYPE_BOOLEAN:
                return (bool) $context->getValue();
        }

        throw new InvalidArgumentException(sprintf('Not valid type cast given, given %s', $type));
    }
}
