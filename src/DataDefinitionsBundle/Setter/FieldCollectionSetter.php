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

namespace Instride\Bundle\DataDefinitionsBundle\Setter;

use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData as AbstractFieldCollection;

class FieldCollectionSetter implements SetterInterface, GetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $keyParts = explode('~', $context->getMapping()->getToColumn());

        $config = $context->getMapping()->getSetterConfig();
        $keys = $config['keys'];
        $fieldName = $config['field'];
        $class = $config['class'];
        $keys = explode(',', $keys);
        $fieldCollectionClass = 'Pimcore\Model\DataObject\Fieldcollection\Data\\' . ucfirst($class);
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
                    $context->getDataRow(),
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
        $fieldCollectionClass = 'Pimcore\Model\DataObject\Fieldcollection\Data\\' . ucfirst($class);
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
