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

namespace Instride\Bundle\DataDefinitionsBundle\EventListener;

use Instride\Bundle\DataDefinitionsBundle\Model\Log;
use Pimcore\Event\Model\DataObjectEvent;

final class ObjectDeleteListener
{
    public function onDataObjectDelete(DataObjectEvent $event): void
    {
        $resource = $event->getObject();

        $list = new Log\Listing();
        $list->setCondition('o_id = ?', $resource->getId());
        $logEntries = $list->load();

        /** @var Log $entry */
        foreach ($logEntries as $entry) {
            $entry->delete();
        }
    }
}
