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

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\Log;

abstract class AbstractCleaner implements CleanerInterface
{
    abstract public function cleanup(DataDefinitionInterface $definition, array $objectIds): void;

    protected function getObjectsToClean(DataDefinitionInterface $definition, array $foundObjectIds): array
    {
        $logs = new Log\Listing();
        $logs->setCondition('definition = ?', [$definition->getId()]);
        $logs = $logs->load();

        $notFound = [];

        /** @var Log $log */
        foreach ($logs as $log) {
            $found = false;

            foreach ($foundObjectIds as $objectId) {
                if ((int)$log->getO_Id() === $objectId) {
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $notFoundObject = Concrete::getById($log->getO_Id());

                if ($notFoundObject instanceof Concrete) {
                    $notFound[] = $notFoundObject;
                }
            }
        }

        $this->deleteLogs($logs);
        $this->writeNewLogs($definition, $foundObjectIds);

        return $notFound;
    }

    protected function deleteLogs(array $logs): void
    {
        /** @var Log $log */
        foreach ($logs as $log) {
            $log->delete();
        }
    }

    protected function writeNewLogs(DataDefinitionInterface $definition, array $objectIds): void
    {
        foreach ($objectIds as $objId) {
            $log = new Log();
            $log->setO_Id((int)$objId);
            $log->setDefinition((int)$definition->getId());
            $log->save();
        }
    }
}

