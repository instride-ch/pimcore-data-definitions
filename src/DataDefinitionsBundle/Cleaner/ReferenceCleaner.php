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

namespace Wvision\Bundle\DataDefinitionsBundle\Cleaner;

use Pimcore\Model\Dependency;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use function count;

class ReferenceCleaner extends AbstractCleaner
{
    public function cleanup(DataDefinitionInterface $definition, array $objectIds)
    {
        $notFoundObjects = $this->getObjectsToClean($definition, $objectIds);

        foreach ($notFoundObjects as $obj) {
            $dependency = $obj->getDependencies();

            if ($dependency instanceof Dependency) {
                if (count($dependency->getRequiredBy()) === 0) {
                    $obj->delete();
                } else {
                    $obj->setPublished(false);
                    $obj->save();
                }
            }
        }
    }
}
