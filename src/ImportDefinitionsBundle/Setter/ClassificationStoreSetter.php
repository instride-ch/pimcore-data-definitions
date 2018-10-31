<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Setter;

use ImportDefinitionsBundle\Getter\GetterInterface;
use Pimcore\Model\DataObject\Concrete;
use ImportDefinitionsBundle\Model\ImportMapping;
use ImportDefinitionsBundle\Model\ExportMapping;

class ClassificationStoreSetter implements SetterInterface, GetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function set(Concrete $object, $value, ImportMapping $map, $data)
    {
        $mapConfig = $map->getSetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int) $mapConfig['keyConfig'];
        $groupConfig = (int) $mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof \Pimcore\Model\DataObject\Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!$groups[$groupConfig]) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                $classificationStore->setLocalizedKeyValue($groupConfig, $keyConfig, $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(Concrete $object, ExportMapping $map, $data)
    {
        $mapConfig = $map->getSetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = (int) $mapConfig['keyConfig'];
        $groupConfig = (int) $mapConfig['groupConfig'];

        $classificationStoreGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof \Pimcore\Model\DataObject\Classificationstore) {
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
