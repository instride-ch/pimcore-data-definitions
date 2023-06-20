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

namespace Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinition\Listing;

use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinition;
use function count;

class Dao extends ExportDefinition\Dao
{
    public function loadList(): array
    {
        $definitions = [];
        foreach ($this->loadIdList() as $name) {
            $definitions[] = ExportDefinition::getByName($name);
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

    public function getTotalCount(): int
    {
        return count($this->loadList());
    }
}
