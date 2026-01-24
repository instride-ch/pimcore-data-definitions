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

namespace Instride\Bundle\DataDefinitionsBundle\Exporter;

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

interface ExporterInterface
{
    public function doExport(ExportDefinitionInterface $definition, array $params);
}
