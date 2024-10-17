<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use Carbon\Carbon;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class CarbonInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): bool|null|\Carbon\Carbon
    {
        if ($context->getValue()) {
            $format = $context->getConfiguration()['date_format'];
            if (!empty($format)) {
                return Carbon::createFromFormat($format, $context->getValue());
            }

            return new Carbon($context->getValue());
        }

        return null;
    }
}
