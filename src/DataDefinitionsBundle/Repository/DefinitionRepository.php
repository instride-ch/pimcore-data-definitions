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

namespace Wvision\Bundle\DataDefinitionsBundle\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreDaoRepository;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

class DefinitionRepository extends PimcoreDaoRepository
{
    public function findByName(string $name): ?DataDefinitionInterface
    {
        $class = $this->metadata->getClass('model');
        $definitionEntry = new $class();
        $definitionEntry->getDao()->getByName($name);

        return $definitionEntry;
    }
}
