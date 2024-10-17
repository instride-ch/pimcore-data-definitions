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

namespace Instride\Bundle\DataDefinitionsBundle\Cleaner;

use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\Log;
use Pimcore\Model\DataObject\Concrete;

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
                if ((int) $log->getO_Id() === $objectId) {
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
            $log->setO_Id((int) $objId);
            $log->setDefinition((int) $definition->getId());
            $log->save();
        }
    }
}
