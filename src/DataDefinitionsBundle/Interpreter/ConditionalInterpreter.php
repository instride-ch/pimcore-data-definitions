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

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Instride\Bundle\DataDefinitionsBundle\Context\ContextFactoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class ConditionalInterpreter implements InterpreterInterface
{
    public function __construct(
        protected ServiceRegistryInterface $interpreterRegistry,
        protected ExpressionLanguage $expressionLanguage,
        protected ContainerInterface $container,
        protected  ContextFactoryInterface $contextFactory
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
            $context->getMapping()
        );

        return $interpreterObject->interpret($newContext);
    }
}
