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
use Instride\Bundle\DataDefinitionsBundle\Exception\InterpreterException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Throwable;

class ExpressionInterpreter implements InterpreterInterface
{
    protected ExpressionLanguage $expressionLanguage;

    protected ContainerInterface $container;

    public function __construct(
        ExpressionLanguage $expressionLanguage,
        ContainerInterface $container,
    ) {
        $this->expressionLanguage = $expressionLanguage;
        $this->container = $container;
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
        $expression = $context->getConfiguration()['expression'];

        try {
            return $this->expressionLanguage->evaluate($expression, [
                'value' => $context->getValue(),
                'object' => $context->getObject(),
                'map' => $context->getMapping(),
                'data' => $context->getDataRow(),
                'data_set' => $context->getDataSet(),
                'definition' => $context->getDefinition(),
                'params' => $context->getParams(),
                'configuration' => $context->getConfiguration(),
                'container' => $this->container,
            ]);
        } catch (Throwable $exception) {
            throw InterpreterException::fromInterpreter(
                $context->getDefinition(),
                $context->getMapping(),
                $context->getParams(),
                $context->getValue(),
                $exception,
            );
        }
    }
}
