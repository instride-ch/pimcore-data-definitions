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

namespace Instride\Bundle\DataDefinitionsBundle\Context;

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

class FetcherContext extends Context implements FetcherContextInterface
{
    public function __construct(
        ExportDefinitionInterface $definition,
        array $params,
        array $configuration,
    ) {
        parent::__construct($definition, $params, $configuration);
    }

    public function getDefinition(): ExportDefinitionInterface
    {
        /**
         * @var ExportDefinitionInterface $definition
         */
        $definition = $this->definition;

        return $definition;
    }
}
