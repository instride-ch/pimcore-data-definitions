<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\EventListener;

use ImportDefinitionsBundle\Model\Log\Listing;
use Pimcore\Event\Model\DataObjectEvent;

final class ObjectDeleteListener
{
    /**
     * @param DataObjectEvent $event
     */
    public function onDataObjectDelete(DataObjectEvent $event)
    {
        $resource = $event->getObject();

        $list = new Listing();
        $list->setCondition('o_id = ?', $resource->getId());
        $logEntries = $list->load();

        foreach ($logEntries as $entry) {
            $entry->delete();
        }
    }
}
