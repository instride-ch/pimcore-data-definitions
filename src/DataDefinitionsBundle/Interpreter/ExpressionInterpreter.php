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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Throwable;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Exception\InterpreterException;

class ExpressionInterpreter implements InterpreterInterface
{
    protected ExpressionLanguage $expressionLanguage;
    protected ContainerInterface $container;

    public function __construct(ExpressionLanguage $expressionLanguage, ContainerInterface $container)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->container = $container;
    }

    public function interpret(InterpreterContextInterface $context): mixed {
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
                $exception
            );
        }
    }
}
