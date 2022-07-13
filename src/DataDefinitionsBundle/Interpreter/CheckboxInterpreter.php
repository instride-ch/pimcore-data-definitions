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
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class CheckboxInterpreter implements InterpreterInterface
{
    public function interpret(
        InterpreterContextInterface $context,
        array $configuration
    ) {
        if (is_string($context->getValue())) {
            if ($context->getValue() === "") {
                return null;
            }

            return filter_var(strtolower($context->getValue()), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return ((bool)$context->getValue());
    }
}
