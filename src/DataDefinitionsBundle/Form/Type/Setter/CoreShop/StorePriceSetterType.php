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

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type\Setter\CoreShop;

use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\ArrayCollection;
use function is_array;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

final class StorePriceSetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stores', StoreChoiceType::class, [
                'multiple' => true,
            ])
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($value) {
                        return $value;
                    },
                    function ($value) {
                        $resolvedValues = [];

                        if (!is_array($value) ||
                            !array_key_exists('stores', $value) ||
                            !$value['stores'] instanceof ArrayCollection) {
                            return [];
                        }

                        foreach ($value['stores'] as $val) {
                            if ($val instanceof StoreInterface) {
                                $resolvedValues[] = $val->getId();
                            }
                        }

                        $value['stores'] = $resolvedValues;

                        return $value;
                    },
                ),
            )
        ;
    }
}
