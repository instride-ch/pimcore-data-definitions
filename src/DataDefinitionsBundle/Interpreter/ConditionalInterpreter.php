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

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\ContextFactoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ConditionalInterpreter implements InterpreterInterface
{
    public function __construct(
        protected ServiceRegistryInterface $interpreterRegistry,
        protected ExpressionLanguage $expressionLanguage,
        protected ContainerInterface $container,
        protected  ContextFactoryInterface $contextFactory,
    ) {
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
        $params = [
            'value' => $context->getValue(),
            'object' => $context->getObject(),
            'map' => $context->getMapping(),
            'data' => $context->getDataRow(),
            'data_set' => $context->getDataSet(),
            'definition' => $context->getDefinition(),
            'params' => $context->getParams(),
            'configuration' => $context->getConfiguration(),
            'container' => $this->container,
        ];

        $condition = $context->getConfiguration()['condition'];

        if ($this->expressionLanguage->evaluate($condition, $params)) {
            $interpreter = $context->getConfiguration()['true_interpreter'];
        } else {
            $interpreter = $context->getConfiguration()['false_interpreter'];
        }

        $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

        if (!$interpreterObject instanceof InterpreterInterface) {
            return $context->getValue();
        }

        $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

        $newContext = $this->contextFactory->createInterpreterContext(
            $context->getDefinition(),
            $context->getParams(),
            $interpreter['interpreterConfig'],
            $context->getDataRow(),
            $context->getDataSet(),
            $context->getObject(),
            $context->getValue(),
            $context->getMapping(),
        );

        return $interpreterObject->interpret($newContext);
    }
}
