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

namespace Instride\Bundle\DataDefinitionsBundle\Form\Type\ProcessManager;

use ProcessManagerBundle\Form\Type\AbstractStartupFormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ExportDefinitionObjectStartupForm extends AbstractStartupFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('root', TextType::class, [
                'required' => false,
            ])
            ->add('query', TextType::class, [
                'required' => false,
            ])
            ->add('only_direct_children', CheckboxType::class, [
                'required' => false,
            ])
            ->add('condition', TextType::class, [
                'required' => false,
            ])
            ->add('ids', CollectionType::class, [
                'allow_add' => true,
                'entry_type' => TextType::class,
                'required' => false,
            ])
        ;
    }
}
