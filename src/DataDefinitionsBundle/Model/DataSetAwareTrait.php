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

namespace Instride\Bundle\DataDefinitionsBundle\Model;

use Instride\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;

trait DataSetAwareTrait
{
    protected ?ImportDataSetInterface $dataSet = null;

    public function getDataSet(): ?ImportDataSetInterface
    {
        return $this->dataSet ?? null;
    }

    public function setDataSet(?ImportDataSetInterface $dataSet): void
    {
        $this->dataSet = $dataSet;
    }
}
