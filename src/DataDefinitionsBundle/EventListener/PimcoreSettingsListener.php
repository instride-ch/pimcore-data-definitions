<?php

declare(strict_types=1);

/*
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\EventListener;

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinition;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;
use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;

class PimcoreSettingsListener
{
    public function indexSettings(IndexActionSettingsEvent $settingsEvent): void
    {
        $settingsEvent->addSetting('data-definitions-import-definition-writeable', (new ImportDefinition())->isWriteable());
        $settingsEvent->addSetting('data-definitions-export-definition-writeable', (new ExportDefinition())->isWriteable());
    }
}
