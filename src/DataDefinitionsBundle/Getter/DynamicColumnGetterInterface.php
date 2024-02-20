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

namespace Instride\Bundle\DataDefinitionsBundle\Getter;

use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;

interface DynamicColumnGetterInterface extends GetterInterface
{
    /**
     * @inheritDoc
     *
     * @return array The key-value array will be merged into the final data set,
     *               with array keys becoming column names.
     *
     *               It's up to the developer to ensure the keys don't collide
     *               with other columns from the definition and to always return
     *               exactly the same keys in exactly the same order for each object.
     */
    public function get(GetterContextInterface $context): array;
}
