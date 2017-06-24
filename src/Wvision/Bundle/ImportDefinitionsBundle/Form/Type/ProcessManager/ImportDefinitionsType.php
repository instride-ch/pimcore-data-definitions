<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Form\Type\ProcessManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ImportDefinitionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('definition', TextType::class)
            ->add('params', TextType::class);
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'process_manager_process_import_definitions';
    }
}
