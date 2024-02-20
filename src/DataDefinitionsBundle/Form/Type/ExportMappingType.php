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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportMapping;

final class ExportMappingType extends AbstractResourceType
{
    private FormTypeRegistryInterface $interpreterTypeRegistry;
    private FormTypeRegistryInterface $getterTypeRegistry;

    public function __construct(
        array $validationGroups,
        FormTypeRegistryInterface $getterTypeRegistry,
        FormTypeRegistryInterface $interpreterTypeRegistry
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
            ->add('interpreter', TextType::class);

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
     * @param string $configurationType
     */
    protected function addGetterConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('getterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addInterpreterConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('interpreterConfig', $configurationType);
    }


    /**
     * @param FormInterface $form
     * @param mixed $data
     * @return string|null
     */
    protected function getGetterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getGetter()) {
            return $data->getGetter();
        }

        return null;
    }

    /**
     * @param FormInterface $form
     * @param mixed $data
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
