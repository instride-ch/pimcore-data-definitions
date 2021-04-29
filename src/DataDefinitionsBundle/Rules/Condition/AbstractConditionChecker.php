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

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Rules\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use InvalidArgumentException;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;
use Wvision\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;

abstract class AbstractConditionChecker implements ImportRuleConditionCheckerInterface
{
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, $params = []): bool
    {
        if (!$rule instanceof ImportRuleInterface) {
            throw new InvalidArgumentException('Import Rule Condition $subject needs to be instance of ImportRuleInterface');
        }

        Assert::keyExists($params, 'object');

        $object = $params['object'];

        Assert::isInstanceOf($object, Concrete::class);

        return $this->isImportRuleValid($rule, $object, $params, $configuration);
    }
}
