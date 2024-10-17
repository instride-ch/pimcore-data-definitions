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
use Pimcore\Model\DataObject\Listing;

class ObjectResolverInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        if (!$context->getValue()) {
            return null;
        }

        $class = 'Pimcore\Model\DataObject\\' . ucfirst($context->getConfiguration()['class']);
        $lookup = 'getBy' . ucfirst($context->getConfiguration()['field']);

        /**
         * @var Listing $listing
         */
        $listing = $class::$lookup($context->getValue());
        $listing->setUnpublished($context->getConfiguration()['match_unpublished']);

        if ($listing->count() === 1) {
            return $listing->current();
        }

        return null;
    }
}
