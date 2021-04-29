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

namespace Wvision\Bundle\DataDefinitionsBundle\Loader;

use InvalidArgumentException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use function count;

class PrimaryKeyLoader implements LoaderInterface
{
    public function load(string $class, $data, DataDefinitionInterface $definition, $params): ?Concrete
    {
        $classObject = '\Pimcore\Model\DataObject\\'.ucfirst($class);
        $classList = '\Pimcore\Model\DataObject\\'.ucfirst($class).'\Listing';

        $list = new $classList();

        if ($list instanceof Listing) {
            $mapping = $definition->getMapping();
            $condition = [];
            $conditionValues = [];
            foreach ($mapping as $map) {
                if ($map->getPrimaryIdentifier()) {
                    $condition[] = '`'.$map->getToColumn().'` = ?';
                    $conditionValues[] = $data[$map->getFromColumn()];
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

                if ($definition->getForceLoadObject()) {
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
