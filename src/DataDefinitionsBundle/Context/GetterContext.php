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
use Instride\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Pimcore\Model\DataObject\Concrete;

class GetterContext extends Context implements GetterContextInterface
{
    public function __construct(
        DataDefinitionInterface $definition,
        array $params,
        array $configuration,
        protected Concrete $object,
        protected ExportMapping $mapping,
    ) {
        parent::__construct($definition, $params, $configuration);
    }

    public function getObject(): Concrete
    {
        return $this->object;
    }

    public function getMapping(): ExportMapping
    {
        return $this->mapping;
    }
}
