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

use Carbon\Carbon;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class CarbonInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): bool|null|\Carbon\Carbon
    {
        if ($context->getValue()) {
            $format = $context->getConfiguration()['date_format'];
            if (!empty($format)) {
                return Carbon::createFromFormat($format, $context->getValue());
            }

            return new Carbon($context->getValue());
        }

        return null;
    }
}
