<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Setter;

use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;

class RelationSetter implements SetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $fieldName = $context->getMapping()->getToColumn();
        $getter = sprintf('get%s', ucfirst($fieldName));
        $setter = sprintf('set%s', ucfirst($fieldName));

        $existingElements = $context->getObject()->$getter();
        if (!is_array($existingElements)) {
            $existingElements = [];
        }

        // Find unique key (path) for all existing elements
        $existingKeys = [];
        foreach ($existingElements as $existingElement) {
            $existingKeys[] = (string) $existingElement;
        }

        $value = $context->getValue();

        if (!is_iterable($value)) {
            $value = [$value];
        }

        // Add all values that does not already exist.
        foreach ($value as $newElement) {
            $newKey = (string) $newElement;
            if (!in_array($newKey, $existingKeys)) {
                $existingElements[] = $newElement;
            }
        }

        $context->getObject()->$setter($existingElements);
    }
}
