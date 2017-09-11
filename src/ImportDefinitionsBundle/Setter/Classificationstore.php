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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Setter;

use Pimcore\Model\Object\Concrete;
use ImportDefinitionsBundle\Model\Mapping;

class Classificationstore implements SetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function set(Concrete $object, $value, Mapping $map, $data)
    {
        $mapConfig = $map->getSetterConfig();
        $fieldName = $mapConfig['field'];
        $keyConfig = intval($mapConfig['keyConfig']);
        $groupConfig = intval($mapConfig['groupConfig']);

        $classificationStoreGetter = "get" . ucfirst($fieldName);

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof \Pimcore\Model\Object\Classificationstore) {
                $groups = $classificationStore->getActiveGroups();

                if (!$groups[$groupConfig]) {
                    $groups[$groupConfig] = true;
                    $classificationStore->setActiveGroups($groups);
                }

                $classificationStore->setLocalizedKeyValue($groupConfig, $keyConfig, $value);
            }
        }
    }
}
