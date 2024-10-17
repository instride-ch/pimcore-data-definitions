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

use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Pimcore\Model\DataObject\Classificationstore;

class ClassificationStoreSetter implements SetterInterface, GetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $mapConfig = $context->getMapping()->getSetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int) $mapConfig['keyConfig'];
        $groupConfig = (int) $mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $classificationStoreGetter)) {
            $classificationStore = $context->getObject()->$classificationStoreGetter();

            if ($classificationStore instanceof Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!($groups[$groupConfig] ?? false)) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                $classificationStore->setLocalizedKeyValue($groupConfig, $keyConfig, $context->getValue());
            }
        }
    }

    public function get(GetterContextInterface $context)
    {
        $mapConfig = $context->getMapping()->getGetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int) $mapConfig['keyConfig'];
        $groupConfig = (int) $mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $classificationStoreGetter)) {
            $classificationStore = $context->getObject()->$classificationStoreGetter();

            if ($classificationStore instanceof Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!($groups[$groupConfig] ?? false)) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                return $classificationStore->getLocalizedKeyValue($groupConfig, $keyConfig);
            }
        }

        return null;
    }
}
