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
use ImportDefinitionsBundle\Model\ExportMapping;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;

class ClassificationStoreFieldGetter implements GetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(Concrete $object, ExportMapping $map, $data)
    {
        $classificationStoreGetter = sprintf('get%s', ucfirst($map->getFromColumn()));

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof \Pimcore\Model\DataObject\Classificationstore) {
                $groups = $classificationStore->getActiveGroups();
                $values = [];

                foreach ($groups as $groupId => $groupIsActive) {
                    if (!$groupIsActive) {
                        continue;
                    }

                    $group = DataObject\Classificationstore\GroupConfig::getById($groupId);
                    $groupRelations = $group->getRelations();

                    foreach ($groupRelations as $keyRelation) {
                        $keyConfig = DataObject\Classificationstore\KeyConfig::getById($keyRelation->getKeyId());

                        foreach (Tool::getValidLanguages() as $language) {
                            $value = $classificationStore->getLocalizedKeyValue($groupId, $keyConfig->getId(),
                                $language);

                            if (is_null($value)) {
                                continue;
                            }

                            $values[sprintf('%s-%s-%s', $groupId, $keyRelation->getKeyId(), $language)] = $value;
                        }
                    }
                }

                return $values;
            }
        }

        return null;
    }
}
