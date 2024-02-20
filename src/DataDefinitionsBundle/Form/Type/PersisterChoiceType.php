<?php

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PersisterChoiceType extends AbstractType
{
    private array $persisters;

    public function __construct(array $persisters)
    {
        $this->persisters = $persisters;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_flip($this->persisters),
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
