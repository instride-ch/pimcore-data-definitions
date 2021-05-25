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

namespace Wvision\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;

final class ImportMappingType extends AbstractResourceType
{
    private FormTypeRegistryInterface $setterTypeRegistry;
    private FormTypeRegistryInterface $interpreterTypeRegistry;

    public function __construct(
        array $validationGroups,
        FormTypeRegistryInterface $setterTypeRegistry,
        FormTypeRegistryInterface $interpreterTypeRegistry
    ) {
        parent::__construct(ImportMapping::class, $validationGroups);

        $this->setterTypeRegistry = $setterTypeRegistry;
        $this->interpreterTypeRegistry = $interpreterTypeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromColumn', TextType::class)
            ->add('toColumn', TextType::class)
            ->add('primaryIdentifier', CheckboxType::class)
            ->add('setter', TextType::class)
            ->add('interpreter', TextType::class);

        /** Setter Configurations */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getSetterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getSetterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('setter')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['setter'])) {
                    return;
                }

                if (!$formType = $this->setterTypeRegistry->get($data['setter'], 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addSetterConfigurationFields($event->getForm(), $formType);
            });

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
            });
    }

    /**
     * @param FormInterface $form
     * @param string        $configurationType
     */
    protected function addSetterConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('setterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param string        $configurationType
     */
    protected function addInterpreterConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('interpreterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param mixed         $data
     * @return string|null
     */
    protected function getSetterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getSetter()) {
            return $data->getSetter();
        }

        return null;
    }

    /**
     * @param FormInterface $form
     * @param mixed         $data
     * @return string|null
     */
    protected function getInterpreterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getInterpreter()) {
            return $data->getInterpreter();
        }

        return null;
    }
}
