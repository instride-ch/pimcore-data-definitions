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

namespace Instride\Bundle\DataDefinitionsBundle\Setter\CoreShop;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Instride\Bundle\DataDefinitionsBundle\Setter\SetterInterface;
use InvalidArgumentException;
use function is_array;

class StorePriceSetter implements SetterInterface, GetterInterface
{
    private $storeRepository;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
    ) {
        $this->storeRepository = $storeRepository;
    }

    public function set(SetterContextInterface $context)
    {
        $config = $context->getMapping()->getSetterConfig();

        if (!array_key_exists('stores', $config) || !is_array($config['stores'])) {
            return;
        }

        foreach ($config['stores'] as $store) {
            $store = $this->storeRepository->find($store);

            if (!$store instanceof StoreInterface) {
                throw new InvalidArgumentException(sprintf('Store with ID %s not found', $config['store']));
            }

            $setter = sprintf('set%s', ucfirst($context->getMapping()->getToColumn()));

            if (!method_exists($context->getObject(), $setter)) {
                throw new InvalidArgumentException(sprintf('Expected a %s function but can not find it', $setter));
            }

            $context->getObject()->$setter($context->getValue(), $store);
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

            $getter = sprintf('get%s', ucfirst($context->getMapping()->getFromColumn()));

            if (!method_exists($context->getObject(), $getter)) {
                throw new InvalidArgumentException(sprintf('Expected a %s function but can not find it', $getter));
            }

            $values[$store->getId()] = $context->getObject()->$getter($store);
        }

        return $values;
    }
}
