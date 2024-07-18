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

namespace Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition\Listing;

use Exception;
use Pimcore;
use Pimcore\Bundle\StaticRoutesBundle\Model\Staticroute;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;
use function count;

class Dao extends ImportDefinition\Dao
{
    public function loadList(): array
    {
        $definitions = [];
        foreach ($this->loadIdList() as $id) {
            $definitions[] = ImportDefinition::getById((int)$id);
        }

        if ($this->model->getFilter()) {
            $definitions = array_filter($definitions, $this->model->getFilter());
        }
        if ($this->model->getOrder()) {
            usort($definitions, $this->model->getOrder());
        }
        $this->model->setObjects($definitions);

        return $definitions;
    }

    public function getAllIds(): array
    {
        return $this->loadIdList();
    }

    public function getTotalCount(): int
    {
        return count($this->loadList());
    }
}
