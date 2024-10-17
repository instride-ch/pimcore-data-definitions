<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ImportDefinitionType extends AbstractResourceType
{
    private FormTypeRegistryInterface $formTypeRegistry;

    public function __construct(
        $dataClass,
        array $validationGroups,
        FormTypeRegistryInterface $formTypeRegistry,
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->formTypeRegistry = $formTypeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('provider', ImportProviderChoiceType::class)
            ->add('loader', LoaderChoiceType::class)
            ->add('class', ClassChoiceType::class)
            ->add('cleaner', CleanerChoiceType::class)
            ->add('filter', FilterChoiceType::class)
            ->add('runner', RunnerChoiceType::class)
            ->add('persister', PersisterChoiceType::class)
            ->add('name', TextType::class)
            ->add('objectPath', TextType::class)
            ->add('key', TextType::class)
            ->add('renameExistingObjects', CheckboxType::class)
            ->add('relocateExistingObjects', CheckboxType::class)
            ->add('createVersion', CheckboxType::class)
            ->add('stopOnException', CheckboxType::class)
            ->add('skipNewObjects', CheckboxType::class)
            ->add('skipExistingObjects', CheckboxType::class)
            ->add('omitMandatoryCheck', CheckboxType::class)
            ->add('failureNotificationDocument', IntegerType::class)
            ->add('successNotificationDocument', IntegerType::class)
            ->add('mapping', ImportMappingCollectionType::class)
            ->add('forceLoadObject', CheckboxType::class)
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());

                if (null === $type) {
                    return;
                }

                if (!$formType = $this->formTypeRegistry->get($type, 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addConfigurationFields($event->getForm(), $formType);
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());

                if (null === $type) {
                    return;
                }

                $event->getForm()->get('provider')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['provider'])) {
                    return;
                }

                if (!$formType = $this->formTypeRegistry->get($data['provider'], 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addConfigurationFields($event->getForm(), $formType);
            })
        ;
    }

    protected function addConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('configuration', $configurationType);
    }

    /**
     * @param mixed $data
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getProvider()) {
            return $data->getProvider();
        }

        if (null !== $form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }
}
