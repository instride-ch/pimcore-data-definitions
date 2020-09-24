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

namespace Wvision\Bundle\DataDefinitionsBundle\Setter;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData as AbstractFieldCollection;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class RelationSetter implements SetterInterface
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function set(Concrete $object, $value, ImportMapping $map, $data)
    {
        $fieldName = $map->getToColumn();
        $getter = sprintf('get%s', ucfirst($fieldName));
        $setter = sprintf('set%s', ucfirst($fieldName));

        $existingElements = $object->$getter();
        if (!is_array($existingElements)) {
            $existingElements = [];
        }
        
        // Find unique key (path) for all existing elements
        $existingKeys = [];
        foreach ($existingElements as $existingElement) {
            $existingKeys[] = (string)$existingElement;
        }


        if (!is_iterable($value)) {
            $value = [$value];
        }

        // Add all values that does not already exist. 
        foreach ($value as $newElement) {
            $newKey = (string)$newElement;
            if (!in_array($newKey, $existingKeys)) {
                $existingElements[] = $newElement;
            }
        }

        $object->$setter($existingElements);
    }
}


