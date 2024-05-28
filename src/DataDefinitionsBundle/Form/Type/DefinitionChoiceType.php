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

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

final class DefinitionChoiceType extends AbstractType
{
    public function __construct(
        private readonly DefinitionRepository $definitionRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(
                [
                    'choices' => function (Options $options) {
                        return array_map(static function (DataDefinitionInterface $def) {
                            return $def->getId();
                        }, $this->definitionRepository->findAll());
                    },
                    'choice_label' => function ($val) {
                        $def = $this->definitionRepository->find($val);

                        return $def !== null ? $def->getName() : null;
                    },
                    'choice_translation_domain' => false,
                    'active' => true,
                ]
            );
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
