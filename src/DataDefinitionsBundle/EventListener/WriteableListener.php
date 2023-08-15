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

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Pimcore\Model\Exception\ConfigWriteException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinition;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinition;

class WriteableListener implements EventSubscriberInterface
{
    public function definitionIsWritable(ResourceControllerEvent $event): void
    {
        $subject = $event->getSubject();

        if ($subject instanceof ImportDefinition && !$subject->isWriteable()) {
            throw new ConfigWriteException();
        }

        if ($subject instanceof ExportDefinition && !$subject->isWriteable()) {
            throw new ConfigWriteException();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'data_definitions.import_definition.pre_save' => 'definitionIsWritable',
            'data_definitions.export_definition.pre_save' => 'definitionIsWritable',
        ];
    }
}
