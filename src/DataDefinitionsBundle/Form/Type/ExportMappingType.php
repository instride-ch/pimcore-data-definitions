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

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ExportMappingType extends AbstractResourceType
{
    private FormTypeRegistryInterface $interpreterTypeRegistry;

    private FormTypeRegistryInterface $getterTypeRegistry;

    public function __construct(
        array $validationGroups,
        FormTypeRegistryInterface $getterTypeRegistry,
        FormTypeRegistryInterface $interpreterTypeRegistry,
    ) {
        parent::__construct(ExportMapping::class, $validationGroups);

        $this->getterTypeRegistry = $getterTypeRegistry;
        $this->interpreterTypeRegistry = $interpreterTypeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromColumn', TextType::class)
            ->add('toColumn', TextType::class)
            ->add('getter', TextType::class)
            ->add('interpreter', TextType::class)
        ;

        /** Getter Configurations */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getGetterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getGetterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('getter')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['getter'])) {
                    return;
                }

                if (!$formType = $this->getterTypeRegistry->get($data['getter'], 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addGetterConfigurationFields($event->getForm(), $formType);
            })
        ;

        /** Interpreter Configurations */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getInterpreterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getInterpreterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('interpreter')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['interpreter'])) {
                    return;
                }

                if (!$formType = $this->interpreterTypeRegistry->get($data['interpreter'], 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addInterpreterConfigurationFields($event->getForm(), $formType);
            })
        ;
    }

    protected function addGetterConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('getterConfig', $configurationType);
    }

    protected function addInterpreterConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('interpreterConfig', $configurationType);
    }

    /**
     * @param mixed $data
     */
    protected function getGetterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getGetter()) {
            return $data->getGetter();
        }

        return null;
    }

    /**
     * @param mixed $data
     */
    protected function getInterpreterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getInterpreter()) {
            return $data->getInterpreter();
        }

        return null;
    }
}
