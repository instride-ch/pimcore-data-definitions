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

namespace Instride\Bundle\DataDefinitionsBundle\Context;

use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;
use Pimcore\Model\DataObject\Concrete;

class FilterContext extends Context implements FilterContextInterface
{
    public function __construct(
        DataDefinitionInterface $definition,
        array $params,
        array $configuration,
        protected array $dataRow,
        protected ImportDataSetInterface $dataSet,
        protected Concrete $object,
    ) {
        parent::__construct($definition, $params, $configuration);
    }

    public function getDataRow(): array
    {
        return $this->dataRow;
    }

    public function getDataSet(): ImportDataSetInterface
    {
        return $this->dataSet;
    }

    public function getObject(): Concrete
    {
        return $this->object;
    }
}
