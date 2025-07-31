<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\ElementsProcessManager;

use Elements\Bundle\ProcessManagerBundle\Model\MonitoringItem;
use Instride\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;

final class ImportListener
{
    public function onTotalEvent(ImportDefinitionEvent $event): void
    {
        $monitoringItem = $this->findMonitoringItem($event);

        if (!$monitoringItem) {
            return;
        }

        $monitoringItem->getLogger()->info('Total: ' . $event->getSubject());
        $monitoringItem->setTotalSteps((int)$event->getSubject());
        $monitoringItem->save();
    }

    public function onProgressEvent(ImportDefinitionEvent $event): void
    {
        $monitoringItem = $this->findMonitoringItem($event);

        if (!$monitoringItem) {
            return;
        }

        $monitoringItem->getLogger()->info('Progress: ' . $event->getSubject());
        $monitoringItem->setCurrentStep($monitoringItem->getCurrentStep() + 1);
        $monitoringItem->save();
    }

    public function onStatusEvent(ImportDefinitionEvent $event): void
    {
        $monitoringItem = $this->findMonitoringItem($event);

        if (!$monitoringItem) {
            return;
        }

        $monitoringItem->getLogger()->info('Status: ' . $event->getSubject());
        $monitoringItem->setMessage((string)$event->getSubject());
        $monitoringItem->save();
    }

    public function onFinishedEvent(ImportDefinitionEvent $event): void
    {
        $monitoringItem = $this->findMonitoringItem($event);

        if (!$monitoringItem) {
            return;
        }

        $monitoringItem->getLogger()->info('Finished: ' . $event->getSubject());
        $monitoringItem->setStatus(MonitoringItem::STATUS_FINISHED);
        $monitoringItem->save();
    }

    public function onFailureEvent(ImportDefinitionEvent $event): void
    {
        $monitoringItem = $this->findMonitoringItem($event);

        if (!$monitoringItem) {
            return;
        }

        $monitoringItem->getLogger()->error('Failed: ' . $event->getSubject());
        $monitoringItem->setMessage((string)$event->getSubject());
        $monitoringItem->setStatus(MonitoringItem::STATUS_FAILED);
        $monitoringItem->save();
    }

    private function findMonitoringItem(ImportDefinitionEvent $event): ?MonitoringItem
    {
        if (!isset($event->getOptions()['monitoringItemId'])) {
            return null;
        }

        $monitoringItemId = (int)$event->getOptions()['monitoringItemId'];

        return MonitoringItem::getById($monitoringItemId);
    }
}
