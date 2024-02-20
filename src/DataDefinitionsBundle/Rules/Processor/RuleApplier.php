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

namespace Instride\Bundle\DataDefinitionsBundle\Rules\Processor;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject\Concrete;
use Instride\Bundle\DataDefinitionsBundle\Rules\Action\ImportRuleProcessorInterface;
use Instride\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;

class RuleApplier implements RuleApplierInterface
{
    private ServiceRegistryInterface $actionServiceRegistry;

    public function __construct(ServiceRegistryInterface $actionServiceRegistry)
    {
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
