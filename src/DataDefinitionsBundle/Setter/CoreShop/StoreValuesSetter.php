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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace WVision\Bundle\DataDefinitionsBundle\Setter\CoreShop;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use WVision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use WVision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use WVision\Bundle\DataDefinitionsBundle\Model\Mapping;
use WVision\Bundle\DataDefinitionsBundle\Setter\SetterInterface;
use Pimcore\Model\DataObject\Concrete;

class StoreValuesSetter implements SetterInterface, GetterInterface
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function set(Concrete $object, $value, Mapping $map, $data)
    {
        $config = $map->getSetterConfig();

        if (!array_key_exists('stores', $config) || !\is_array($config['stores'])) {
            return;
        }

        foreach ($config['stores'] as $store) {
            $store = $this->storeRepository->find($store);

            if (!$store instanceof StoreInterface) {
                throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $config['store']));
            }

            $setter = sprintf('set%sOfType', ucfirst($map->getToColumn()));

            if (!method_exists($object, $setter)) {
                throw new \InvalidArgumentException(sprintf('Expected a %s function but can not find it', $setter));
            }

            $object->$setter($config['type'], $value, $store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(Concrete $object, ExportMapping $map, $data)
    {
        $config = $map->getGetterConfig();

        if (!array_key_exists('stores', $config) || !\is_array($config['stores'])) {
            return [];
        }

        $values = [];

        foreach ($config['stores'] as $store) {
            $store = $this->storeRepository->find($store);

            if (!$store instanceof StoreInterface) {
                throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $config['store']));
            }

            $getter = sprintf('get%sOfType', ucfirst($map->getFromColumn()));

            if (!method_exists($object, $getter)) {
                throw new \InvalidArgumentException(sprintf('Expected a %s function but can not find it', $getter));
            }

            $values[$store->getId()] = $object->$getter($config['type'], $store);
        }

        return $values;
    }
}

class_alias(StoreValuesSetter::class, 'ImportDefinitionsBundle\Setter\CoreShop\StoreValuesSetter');
