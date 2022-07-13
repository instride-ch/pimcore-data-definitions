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

namespace Wvision\Bundle\DataDefinitionsBundle\Setter;

use Pimcore\Model\DataObject\Classificationstore;
use Wvision\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;

class ClassificationStoreSetter implements SetterInterface, GetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $mapConfig = $context->getImportMapping()->getSetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int)$mapConfig['keyConfig'];
        $groupConfig = (int)$mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $classificationStoreGetter)) {
            $classificationStore = $context->getObject()->$classificationStoreGetter();

            if ($classificationStore instanceof Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!$groups[$groupConfig]) {
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
        $keyConfig = (int)$mapConfig['keyConfig'];
        $groupConfig = (int)$mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $classificationStoreGetter)) {
            $classificationStore = $context->getObject()->$classificationStoreGetter();

            if ($classificationStore instanceof Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!$groups[$groupConfig]) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                return $classificationStore->getLocalizedKeyValue($groupConfig, $keyConfig);
            }
        }

        return null;
    }
}
