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

use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData as AbstractFieldCollection;
use Wvision\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;

class FieldCollectionSetter implements SetterInterface, GetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $keyParts = explode('~', $context->getImportMapping()->getToColumn());

        $config = $context->getImportMapping()->getSetterConfig();
        $keys = $config['keys'];
        $fieldName = $config['field'];
        $class = $config['class'];
        $keys = explode(',', $keys);
        $fieldCollectionClass = 'Pimcore\Model\DataObject\Fieldcollection\Data\\'.ucfirst($class);
        $field = $keyParts[3];
        $mappedKeys = [];

        foreach ($keys as $key) {
            $tmp = explode(':', $key);

            $mappedKeys[] = [
                'from' => $tmp[0],
                'to' => $tmp[1],
            ];
        }

        $getter = sprintf('get%s', ucfirst($fieldName));
        $setter = sprintf('set%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $getter)) {
            $fieldCollection = $context->getObject()->$getter();

            if (!$fieldCollection instanceof Fieldcollection) {
                $fieldCollection = new Fieldcollection();
            }

            $items = $fieldCollection->getItems();
            $found = false;

            foreach ($items as $item) {
                if (is_a($item, $fieldCollectionClass) && $this->isValidKey(
                        $mappedKeys,
                        $item,
                        $context->getDataRow()
                    )) {
                    if ($item instanceof AbstractFieldCollection) {
                        $item->setValue($field, $context->getValue());
                    }

                    $found = true;
                }
            }

            if (!$found) {
                // Create new entry
                $item = new $fieldCollectionClass();

                if ($item instanceof AbstractFieldCollection) {
                    foreach ($mappedKeys as $key) {
                        $item->setValue($key['to'], $context->getDataRow()[$key['from']]);
                    }

                    $item->setValue($field, $context->getValue());

                    $fieldCollection->add($item);
                }
            }

            $context->getObject()->$setter($fieldCollection);
        }
    }

    public function get(GetterContextInterface $context)
    {
        $keyParts = explode('~', $context->getMapping()->getFromColumn());

        $config = $context->getMapping()->getGetterConfig();
        $fieldName = $config['field'];
        $class = $config['class'];
        $fieldCollectionClass = 'Pimcore\Model\DataObject\Fieldcollection\Data\\'.ucfirst($class);
        $field = $keyParts[3];

        $getter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $getter)) {
            $fieldCollection = $context->getObject()->$getter();

            if (!$fieldCollection instanceof Fieldcollection) {
                return null;
            }

            $items = $fieldCollection->getItems();
            $values = [];

            foreach ($items as $item) {
                if (!$item instanceof AbstractFieldCollection) {
                    continue;
                }

                if (!is_a($item, $fieldCollectionClass)) {
                    continue;
                }

                $getter = sprintf('get%s', ucfirst($field));

                if (method_exists($item, $getter)) {
                    $values[$item->getIndex()] = $item->$getter();
                }
            }

            return $values;
        }

        return null;
    }

    protected function isValidKey(array $keys, AbstractFieldCollection $fieldcollection, array $data): bool
    {
        foreach ($keys as $key) {
            $getter = sprintf('get%s', ucfirst($key['to']));

            if (method_exists($fieldcollection, $getter)) {
                $keyValue = $fieldcollection->$getter();

                if ($keyValue !== $data[$key['from']]) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }
}
