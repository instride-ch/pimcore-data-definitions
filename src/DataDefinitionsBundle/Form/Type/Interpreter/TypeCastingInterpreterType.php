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

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type\Interpreter;

use Instride\Bundle\DataDefinitionsBundle\Interpreter\TypeCastingInterpreter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class TypeCastingInterpreterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('toType', ChoiceType::class, [
                'choices' => [
                    TypeCastingInterpreter::TYPE_INT => TypeCastingInterpreter::TYPE_INT,
                    TypeCastingInterpreter::TYPE_FLOAT => TypeCastingInterpreter::TYPE_FLOAT,
                    TypeCastingInterpreter::TYPE_STRING => TypeCastingInterpreter::TYPE_STRING,
                    TypeCastingInterpreter::TYPE_BOOLEAN => TypeCastingInterpreter::TYPE_BOOLEAN,
                ],
            ])
        ;
    }
}
