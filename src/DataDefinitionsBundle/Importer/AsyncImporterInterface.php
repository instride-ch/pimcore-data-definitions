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

namespace Instride\Bundle\DataDefinitionsBundle\Importer;

use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

interface AsyncImporterInterface
{
    public function doImportRowAsync(ImportDefinitionInterface $definition, array $row, array $params): void;

    public function doImportAsync(ImportDefinitionInterface $definition, array $params): void;
}
