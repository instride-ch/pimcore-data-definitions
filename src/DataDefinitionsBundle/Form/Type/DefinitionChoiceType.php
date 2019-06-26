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

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use WVision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DefinitionChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $definitionRepository;

    /**
     * @param RepositoryInterface $definitionRepository
     */
    public function __construct(RepositoryInterface $definitionRepository)
    {
        $this->definitionRepository = $definitionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'choices' => function (Options $options) {
                        return array_map(function(DefinitionInterface $def) {
                            return $def->getId();
                        }, $this->definitionRepository->findAll());
                    },
                    'choice_label' => function($val) {
                        $def = $this->definitionRepository->find($val);

                        return $def->getName();
                    },
                    'choice_translation_domain' => false,
                    'active' => true,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'import_definitions_definition_choice';
    }
}

class_alias(DefinitionChoiceType::class, 'ImportDefinitionsBundle\Form\Type\DefinitionChoiceType');
