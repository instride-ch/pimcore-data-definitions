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
use Webmozart\Assert\Assert;

final class NestedInterpreter implements InterpreterInterface
{
    public function __construct(
        private ServiceRegistryInterface $interpreterRegistry,
        private ContextFactoryInterface $contextFactory,
    ) {
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
        Assert::keyExists($context->getConfiguration(), 'interpreters');
        Assert::isArray($context->getConfiguration()['interpreters'], 'Interpreter Config needs to be array');

        $value = $context->getValue();

        foreach ($context->getConfiguration()['interpreters'] as $interpreter) {
            $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);
            $newContext = $this->contextFactory->createInterpreterContext(
                $context->getDefinition(),
                $context->getParams(),
                $interpreter['interpreterConfig'],
                $context->getDataRow(),
                $context->getDataSet(),
                $context->getObject(),
                $value,
                $context->getMapping(),
            );

            $value = $interpreterObject->interpret($newContext);
        }

        return $value;
    }
}
