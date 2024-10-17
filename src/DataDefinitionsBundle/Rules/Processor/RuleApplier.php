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

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Instride\Bundle\DataDefinitionsBundle\Rules\Action\ImportRuleProcessorInterface;
use Instride\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;
use Pimcore\Model\DataObject\Concrete;

class RuleApplier implements RuleApplierInterface
{
    private ServiceRegistryInterface $actionServiceRegistry;

    public function __construct(
        ServiceRegistryInterface $actionServiceRegistry,
    ) {
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    public function applyRule(ImportRuleInterface $rule, Concrete $concrete, $value, array $params)
    {
        foreach ($rule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof ImportRuleProcessorInterface) {
                $value = $processor->apply($rule, $concrete, $value, $action->getConfiguration(), $params);
            }
        }

        return $value;
    }
}
