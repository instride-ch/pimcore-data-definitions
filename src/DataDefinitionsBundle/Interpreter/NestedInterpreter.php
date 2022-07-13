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

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;
use Wvision\Bundle\DataDefinitionsBundle\Context\ContextFactoryInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

final class NestedInterpreter implements InterpreterInterface
{
    public function __construct(
        private ServiceRegistryInterface $interpreterRegistry,
        private ContextFactoryInterface $contextFactory
    ) {
    }

    public function interpret(
        InterpreterContextInterface $context,
        array $configuration
    ) {
        Assert::keyExists($configuration, 'interpreters');
        Assert::isArray($configuration['interpreters'], 'Interpreter Config needs to be array');

        $value = $context->getValue();

        foreach ($configuration['interpreters'] as $interpreter) {
            $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);
            $newContext = $this->contextFactory->createInterpreterContext(
                $context->getDefinition(),
                $context->getParams(),
                $context->getDataRow(),
                $context->getDataSet(),
                $context->getObject(),
                $value,
                $context->getMapping()
            );

            $value = $interpreterObject->interpret($newContext, $interpreter['interpreterConfig']);
        }

        return $value;
    }
}
