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

namespace Instride\Bundle\DataDefinitionsBundle\Loader;

use function count;
use Instride\Bundle\DataDefinitionsBundle\Context\LoaderContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use InvalidArgumentException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;

class PrimaryKeyLoader implements LoaderInterface
{
    public function load(LoaderContextInterface $context): ?Concrete
    {
        $classObject = '\Pimcore\Model\DataObject\\' . ucfirst($context->getClass());
        $classList = '\Pimcore\Model\DataObject\\' . ucfirst($context->getClass()) . '\Listing';

        $list = new $classList();

        if ($list instanceof Listing) {
            /**
             * @var ImportMapping[] $mapping
             */
            $mapping = $context->getDefinition()->getMapping();
            $condition = [];
            $conditionValues = [];
            foreach ($mapping as $map) {
                if ($map->getPrimaryIdentifier()) {
                    $condition[] = '`' . $map->getToColumn() . '` = ?';
                    $conditionValues[] = $context->getDataRow()[$map->getFromColumn()];
                }
            }

            if (count($condition) === 0) {
                throw new InvalidArgumentException('No primary identifier defined!');
            }

            $list->setUnpublished(true);
            $list->setCondition(implode(' AND ', $condition), $conditionValues);
            $list->setObjectTypes([
                Concrete::OBJECT_TYPE_VARIANT,
                Concrete::OBJECT_TYPE_OBJECT,
                Concrete::OBJECT_TYPE_FOLDER,
            ]);
            $list->load();
            $objectData = $list->getObjects();

            if (count($objectData) > 1) {
                throw new InvalidArgumentException('Object with the same primary key was found multiple times');
            }

            if (count($objectData) === 1) {
                $obj = $objectData[0];

                if ($context->getDefinition()->getForceLoadObject()) {
                    $obj = DataObject::getById($obj->getId(), true);

                    if (!$obj instanceof $classObject) {
                        $obj = new $classObject();
                    }
                }

                return $obj;
            }
        }

        return null;
    }
}
