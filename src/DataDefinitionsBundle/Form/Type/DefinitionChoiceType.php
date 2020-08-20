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

namespace Wvision\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

final class DefinitionChoiceType extends AbstractType
{
    private $definitionRepository;

    public function __construct(RepositoryInterface $definitionRepository)
    {
        $this->definitionRepository = $definitionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'choices' => function (Options $options) {
                        return array_map(function (DataDefinitionInterface $def) {
                            return $def->getId();
                        }, $this->definitionRepository->findAll());
                    },
                    'choice_label' => function ($val) {
                        $def = $this->definitionRepository->find($val);

                        return $def->getName();
                    },
                    'choice_translation_domain' => false,
                    'active' => true,
                ]
            );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

