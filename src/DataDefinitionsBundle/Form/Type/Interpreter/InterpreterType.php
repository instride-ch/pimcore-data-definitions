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

namespace Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wvision\Bundle\DataDefinitionsBundle\Form\Type\InterpreterChoiceType;
use Wvision\Bundle\DataDefinitionsBundle\Form\Type\NoConfigurationType;

final class InterpreterType extends AbstractType
{
    private FormTypeRegistryInterface $formTypeRegistry;

    public function __construct(FormTypeRegistryInterface $formTypeRegistry)
    {
        $this->formTypeRegistry = $formTypeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', InterpreterChoiceType::class);

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

                $event->getForm()->get('type')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['type'])) {
                    return;
                }

                if (!$formType = $this->formTypeRegistry->get($data['type'], 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addConfigurationFields($event->getForm(), $formType);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('configuration_type', null)
            ->setAllowedTypes('configuration_type', ['string', 'null']);
    }

    /**
     * @param FormInterface $form
     * @param string        $configurationType
     */
    protected function addConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('interpreterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param mixed         $data
     *
     * @return string|null
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data['type']) {
            return $data['type'];
        }

        return null;
    }
}
