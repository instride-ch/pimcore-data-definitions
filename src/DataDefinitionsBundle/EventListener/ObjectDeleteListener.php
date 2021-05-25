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

namespace Wvision\Bundle\DataDefinitionsBundle\EventListener;

use Pimcore\Event\Model\DataObjectEvent;
use Wvision\Bundle\DataDefinitionsBundle\Model\Log;

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
