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

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContext;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class AssetsUrlInterpreter extends AssetUrlInterpreter
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        $assets = [];
        foreach ((array) $context->getValue() as $item) {
            $childContext = new InterpreterContext(
                $context->getDefinition(),
                $context->getParams(),
                $context->getConfiguration(),
                $context->getDataRow(),
                $context->getDataSet(),
                $context->getObject(),
                $item,
                $context->getMapping(),
            );
            $asset = parent::interpret($childContext);

            if ($asset) {
                $assets[] = $asset;
            }
        }

        return $assets ?: null;
    }
}
