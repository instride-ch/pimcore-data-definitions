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

use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;

class LoaderContext extends Context implements LoaderContextInterface
{
    public function __construct(
        ImportDefinitionInterface $definition,
        array $params,
        array $configuration,
        protected array $dataRow,
        protected ImportDataSetInterface $dataSet,
        protected string $class,
    ) {
        parent::__construct($definition, $params, $configuration);
    }

    public function getDefinition(): ImportDefinitionInterface
    {
        /**
         * @var ImportDefinitionInterface $definition
         */
        $definition = $this->definition;

        return $definition;
    }

    public function getDataRow(): array
    {
        return $this->dataRow;
    }

    public function getDataSet(): ImportDataSetInterface
    {
        return $this->dataSet;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
