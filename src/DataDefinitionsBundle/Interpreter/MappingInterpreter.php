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

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class MappingInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        $interpreterMap = $context->getConfiguration()['mapping'];
        $resolvedMap = [];

        if (!is_array($interpreterMap)) {
            $interpreterMap = [];
        }

        foreach ($interpreterMap as $itemMap) {
            $resolvedMap[$itemMap['from']] = $itemMap['to'];
        }

        if (array_key_exists($context->getValue(), $resolvedMap)) {
            return $resolvedMap[$context->getValue()];
        }

        if ($context->getConfiguration()['return_null_when_not_found']) {
            return null;
        }

        return $context->getValue();
    }
}
