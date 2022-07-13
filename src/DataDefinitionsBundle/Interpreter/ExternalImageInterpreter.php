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

use Pimcore\Model\DataObject\Data\ExternalImage;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

class ExternalImageInterpreter implements InterpreterInterface
{
    public function interpret(
        InterpreterContextInterface $context,
        array $configuration
    ) {
        if (($context->getDefinition() instanceof ExportDefinitionInterface) && $context->getValue(
            ) instanceof ExternalImage) {
            return $context->getValue()->getUrl();
        }

        if (($context->getDefinition() instanceof ImportDefinitionInterface) && filter_var(
                $context->getValue(),
                FILTER_VALIDATE_URL
            )) {
            return new ExternalImage($context->getValue());
        }

        return null;
    }
}
