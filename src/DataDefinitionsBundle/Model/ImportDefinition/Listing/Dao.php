<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition\Listing;

use function count;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;

class Dao extends ImportDefinition\Dao
{
    public function loadList(): array
    {
        $definitions = [];
        foreach ($this->loadIdList() as $id) {
            $definitions[] = ImportDefinition::getById((int) $id);
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
