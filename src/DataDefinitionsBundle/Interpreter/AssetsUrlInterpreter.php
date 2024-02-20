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

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContext;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class AssetsUrlInterpreter extends AssetUrlInterpreter
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        $assets = [];
        foreach ((array)$context->getValue() as $item) {
            $childContext = new InterpreterContext(
                $context->getDefinition(),
                $context->getParams(),
                $context->getConfiguration(),
                $context->getDataRow(),
                $context->getDataSet(),
                $context->getObject(),
                $item,
                $context->getMapping()
            );
            $asset = parent::interpret($childContext);

            if ($asset) {
                $assets[] = $asset;
            }
        }

        return $assets ?: null;
    }
}
