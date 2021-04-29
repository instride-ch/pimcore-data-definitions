<?php

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter\CoreShop;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class MoneyInterpreterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isFloat', CheckboxType::class)
            ->add('currency', CurrencyChoiceType::class)
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return $value;
                },
                function ($value) {

                    if (isset($value['currency']) && $value['currency'] instanceof CurrencyInterface) {
                        $value['currency'] = $value['currency']->getId();
                    }

                    return $value;
                }
            ));
    }
}
