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

namespace Instride\Bundle\DataDefinitionsBundle\Rules\Processor;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;
use Pimcore\Model\DataObject\Concrete;

class ImportRuleValidationProcessor implements ImportRuleValidationProcessorInterface
{
    private RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor;

    public function __construct(
        RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor,
    ) {
        $this->ruleConditionsValidationProcessor = $ruleConditionsValidationProcessor;
    }

    public function isImportRuleValid(
        DataDefinitionInterface $definition,
        Concrete $object,
        ImportRuleInterface $importRule,
        array $params,
    ): bool {
        $params['object'] = $object;

        return $this->isValid($definition, $importRule, $params);
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, $params = []): bool
    {
        return $this->ruleConditionsValidationProcessor->isValid(
            $subject,
            $rule,
            $rule->getConditions(),
            $params,
        );
    }
}
