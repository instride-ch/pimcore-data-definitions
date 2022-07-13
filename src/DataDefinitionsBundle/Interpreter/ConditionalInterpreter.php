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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class ConditionalInterpreter implements InterpreterInterface
{
    private ServiceRegistryInterface $interpreterRegistry;
    protected ExpressionLanguage $expressionLanguage;
    protected ContainerInterface $container;

    public function __construct(
        ServiceRegistryInterface $interpreterRegistry,
        ExpressionLanguage $expressionLanguage,
        ContainerInterface $container
    ) {
        $this->interpreterRegistry = $interpreterRegistry;
        $this->expressionLanguage = $expressionLanguage;
        $this->container = $container;
    }

    public function interpret(
        InterpreterContextInterface $context,
        array $configuration
    ) {
        $params = [
            'value' => $context->getValue(),
            'object' => $context->getObject(),
            'map' => $context->getMapping(),
            'data' => $context->getDataRow(),
            'data_set' => $context->getDataSet(),
            'definition' => $context->getDefinition(),
            'params' => $context->getParams(),
            'configuration' => $configuration,
            'container' => $this->container,
        ];

        $condition = $configuration['condition'];

        if ($this->expressionLanguage->evaluate($condition, $params)) {
            $interpreter = $configuration['true_interpreter'];
        } else {
            $interpreter = $configuration['false_interpreter'];
        }

        $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

        if (!$interpreterObject instanceof InterpreterInterface) {
            return $context->getValue();
        }

        return $interpreterObject->interpret($context, $interpreter['interpreterConfig']);
    }
}
