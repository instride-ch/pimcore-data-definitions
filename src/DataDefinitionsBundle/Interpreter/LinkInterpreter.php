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

use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Element\ElementInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

class LinkInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        if (($context->getDefinition() instanceof ExportDefinitionInterface) && $context->getValue() instanceof Link) {
            return $context->getValue()->getHref();
        }

        if (($context->getDefinition() instanceof ImportDefinitionInterface)) {
            $link = new Link();

            if (filter_var($context->getValue(), FILTER_VALIDATE_URL)) {
                $link->setDirect($context->getValue());
            }

            $link->setText($context->getValue());

            if ($context->getValue() instanceof ElementInterface) {
                $link->setElement($context->getValue());
            }

            return $link;
        }

        return null;
    }
}
