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

namespace Instride\Bundle\DataDefinitionsBundle\Rules\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionType;
use Symfony\Component\Form\FormBuilderInterface;

final class ImportRuleActionType extends RuleActionType
{
    public function __construct(
        array $validationGroups,
        FormTypeRegistryInterface $formTypeRegistry,
    ) {
        parent::__construct('', $validationGroups, $formTypeRegistry);
    }

    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', ImportRuleActionChoiceType::class, [
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }
}
