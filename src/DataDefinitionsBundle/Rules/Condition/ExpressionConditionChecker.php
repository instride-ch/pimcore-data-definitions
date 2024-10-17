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

namespace Instride\Bundle\DataDefinitionsBundle\Rules\Condition;

use Instride\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionConditionChecker extends AbstractConditionChecker
{
    protected ExpressionLanguage $expressionLanguage;

    protected ContainerInterface $container;

    public function __construct(
        ExpressionLanguage $expressionLanguage,
        ContainerInterface $container,
    ) {
        $this->expressionLanguage = $expressionLanguage;
        $this->container = $container;
    }

    public function isImportRuleValid(
        ImportRuleInterface $subject,
        Concrete $concrete,
        array $params,
        array $configuration,
    ): bool {
        $expression = $configuration['expression'];

        return $this->expressionLanguage->evaluate(
            $expression,
            array_merge($params, ['container' => $this->container]),
        );
    }
}
