<?php


namespace Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter\CoreShop;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class MoneyInterpreterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isFloat', CheckboxType::class)
            ->add('currency', IntegerType::class);
    }
}