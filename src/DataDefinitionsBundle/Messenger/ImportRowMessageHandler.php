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

namespace Instride\Bundle\DataDefinitionsBundle\Messenger;

use Instride\Bundle\DataDefinitionsBundle\Importer\AsyncImporterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;

class ImportRowMessageHandler
{
    public function __construct(
        private AsyncImporterInterface $importer,
    ) {
    }

    public function __invoke(ImportRowMessage $message): void
    {
        $definition = ImportDefinition::getById($message->getDefinitionId());

        if (!$definition) {
            throw new \InvalidArgumentException('Invalid definition id');
        }

        $this->importer->doImportRowAsync(
            $definition,
            $message->getData(),
            $message->getParams(),
        );
    }
}
