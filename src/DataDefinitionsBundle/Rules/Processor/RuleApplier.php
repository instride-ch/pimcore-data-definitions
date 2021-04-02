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
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Rules\Processor;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Rules\Action\ImportRuleProcessorInterface;
use Wvision\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;

class RuleApplier implements RuleApplierInterface
{
    private $actionServiceRegistry;

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
