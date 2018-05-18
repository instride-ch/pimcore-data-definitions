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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Form\Type\Interpreter\CoreShop;

use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

final class StoresInterpreterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stores', StoreChoiceType::class, [
                'multiple' => true,
            ])
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return $value;
                },
                function ($value) {
                    $resolvedValues = [];

                    if (!\is_array($value) ||
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
                }
            ))
        ;
    }
}
