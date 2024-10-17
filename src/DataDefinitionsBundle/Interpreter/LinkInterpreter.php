<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Element\ElementInterface;

class LinkInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        if (($context->getDefinition() instanceof ExportDefinitionInterface) && $context->getValue() instanceof Link) {
            return $context->getValue()->getHref();
        }

        if (($context->getDefinition() instanceof ImportDefinitionInterface)) {
            $link = new Link();

            if (filter_var($context->getValue(), \FILTER_VALIDATE_URL)) {
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
