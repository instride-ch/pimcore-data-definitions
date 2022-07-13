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

namespace Wvision\Bundle\DataDefinitionsBundle\Setter\CoreShop;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use InvalidArgumentException;
use Wvision\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Setter\SetterInterface;
use function is_array;

class StoreValuesSetter implements SetterInterface, GetterInterface
{
    private $storeRepository;

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function set(SetterContextInterface $context)
    {
        $config = $context->getImportMapping()->getSetterConfig();

        if (!array_key_exists('stores', $config) || !is_array($config['stores'])) {
            return;
        }

        foreach ($config['stores'] as $store) {
            $store = $this->storeRepository->find($store);

            if (!$store instanceof StoreInterface) {
                throw new InvalidArgumentException(sprintf('Store with ID %s not found', $config['store']));
            }

            $setter = sprintf('set%sOfType', ucfirst($context->getImportMapping()->getToColumn()));

            if (!method_exists($context->getObject(), $setter)) {
                throw new InvalidArgumentException(sprintf('Expected a %s function but can not find it', $setter));
            }

            $context->getObject()->$setter($config['type'], $context->getValue(), $store);
        }
    }

    public function get(GetterContextInterface $context)
    {
        $config = $context->getMapping()->getGetterConfig();

        if (!array_key_exists('stores', $config) || !is_array($config['stores'])) {
            return [];
        }

        $values = [];

        foreach ($config['stores'] as $store) {
            $store = $this->storeRepository->find($store);

            if (!$store instanceof StoreInterface) {
                throw new InvalidArgumentException(sprintf('Store with ID %s not found', $config['store']));
            }

            $getter = sprintf('get%sOfType', ucfirst($context->getMapping()->getFromColumn()));

            if (!method_exists($context->getObject(), $getter)) {
                throw new InvalidArgumentException(sprintf('Expected a %s function but can not find it', $getter));
            }

            $values[$store->getId()] = $context->getObject()->$getter($config['type'], $store);
        }

        return $values;
    }
}
