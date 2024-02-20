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

namespace Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ImportRuleConditionPass extends RegisterRegistryTypePass
{
    public const IMPORT_RULE_CONDITION = 'data_definitions.import_rule.condition';

    public function __construct()
    {
        parent::__construct(
            'data_definitions.import_rule.condition',
            'data_definitions.form.import_rule.condition',
            'data_definitions.import_rule.conditions',
            self::IMPORT_RULE_CONDITION
        );
    }
}
