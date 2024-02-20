<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright 2024 instride AG (https://instride.ch)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Service;

interface ClassStorageInterface
{
    public function get(string $className): string;

    public function has(string $className): bool;

    public function set(string $className): string;
}
