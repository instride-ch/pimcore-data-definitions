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

namespace ImportDefinitionsBundle\Setter\CoreShop;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use ImportDefinitionsBundle\Model\Mapping;
use ImportDefinitionsBundle\Setter\SetterInterface;
use Pimcore\Model\DataObject\Concrete;

class StorePriceSetter implements SetterInterface
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

        $store = $this->storeRepository->find($config['store']);

        if (!$store instanceof StoreInterface) {
            throw new \InvalidArgumentException(sprintf('Store with ID %s not found', $config['store']));
        }

        $setter = 'set' . ucfirst($map->getToColumn());

        if (!method_exists($object, $setter)) {
            throw new \InvalidArgumentException(sprintf('Expected a %s function but can not find it', $setter));
        }

        $object->$setter($value, $store);
    }
}