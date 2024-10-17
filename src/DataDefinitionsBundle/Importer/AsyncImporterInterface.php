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

namespace Instride\Bundle\DataDefinitionsBundle\Importer;

use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

interface AsyncImporterInterface
{
    public function doImportRowAsync(ImportDefinitionInterface $definition, array $row, array $params): void;

    public function doImportAsync(ImportDefinitionInterface $definition, array $params): void;
}
