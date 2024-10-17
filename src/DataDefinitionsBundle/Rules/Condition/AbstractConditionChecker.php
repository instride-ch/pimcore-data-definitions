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

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Instride\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;
use InvalidArgumentException;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

abstract class AbstractConditionChecker implements ImportRuleConditionCheckerInterface
{
    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = [],
    ): bool {
        if (!$rule instanceof ImportRuleInterface) {
            throw new InvalidArgumentException(
                'Import Rule Condition $subject needs to be instance of ImportRuleInterface',
            );
        }

        Assert::keyExists($params, 'object');

        $object = $params['object'];

        Assert::isInstanceOf($object, Concrete::class);

        return $this->isImportRuleValid($rule, $object, $params, $configuration);
    }
}
