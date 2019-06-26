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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace WVision\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use WVision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ExportMappingType extends AbstractResourceType
{
    /**
     * @var FormTypeRegistryInterface
     */
    private $interpreterTypeRegistry;

    /**
     * @var FormTypeRegistryInterface
     */
    private $getterTypeRegistry;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $validationGroups,
        FormTypeRegistryInterface $getterTypeRegistry,
        FormTypeRegistryInterface $interpreterTypeRegistry
    )
    {
        parent::__construct(ExportMapping::class, $validationGroups);

        $this->getterTypeRegistry = $getterTypeRegistry;
        $this->interpreterTypeRegistry = $interpreterTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

                $this->addGetterConfigurationFields($event->getForm(), $this->getterTypeRegistry->get($data['getter'], 'default'));
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

                $this->addInterpreterConfigurationFields($event->getForm(), $this->interpreterTypeRegistry->get($data['interpreter'], 'default'));
            });
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addGetterConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('getterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addInterpreterConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('interpreterConfig', $configurationType);
    }


    /**
     * @param FormInterface $form
     * @param mixed $data
     * @return string|null
     */
    protected function getGetterRegistryIdentifier(FormInterface $form, $data = null)
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
    protected function getInterpreterRegistryIdentifier(FormInterface $form, $data = null)
    {
        if (null !== $data && null !== $data->getInterpreter()) {
            return $data->getInterpreter();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'import_definitions_mapping';
    }
}

class_alias(ExportMappingType::class, 'ImportDefinitionsBundle\Form\Type\ExportMappingType');
