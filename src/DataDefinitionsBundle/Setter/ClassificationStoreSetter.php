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
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;

class ClassificationStoreSetter implements SetterInterface, GetterInterface
{
    public function set(Concrete $object, $value, ImportMapping $map, $data): void
    {
        $mapConfig = $map->getSetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int)$mapConfig['keyConfig'];
        $groupConfig = (int)$mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!$groups[$groupConfig]) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                $classificationStore->setLocalizedKeyValue($groupConfig, $keyConfig, $value);
            }
        }
    }

    public function get(Concrete $object, ExportMapping $map, $data)
    {
        $mapConfig = $map->getGetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int)$mapConfig['keyConfig'];
        $groupConfig = (int)$mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

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
