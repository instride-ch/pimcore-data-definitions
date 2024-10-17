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
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Instride\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use Instride\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;
use Pimcore\Model\DataObject\Concrete;

class ContextFactory implements ContextFactoryInterface
{
    public function createFetcherContext(
        ExportDefinitionInterface $definition,
        array $params,
        array $configuration,
    ): FetcherContextInterface {
        return new FetcherContext($definition, $params, $configuration);
    }

    public function createLoaderContext(
        ImportDefinitionInterface $definition,
        array $params,
        array $dataRow,
        ImportDataSetInterface $dataSet,
        string $class,
    ): LoaderContextInterface {
        return new LoaderContext($definition, $params, [], $dataRow, $dataSet, $class);
    }

    public function createFilterContext(
        DataDefinitionInterface $definition,
        array $params,
        array $dataRow,
        ImportDataSetInterface $dataSet,
        Concrete $object,
    ): FilterContextInterface {
        return new FilterContext($definition, $params, [], $dataRow, $dataSet, $object);
    }

    public function createGetterContext(
        DataDefinitionInterface $definition,
        array $params,
        Concrete $object,
        ExportMapping $mapping,
    ): GetterContextInterface {
        return new GetterContext($definition, $params, [], $object, $mapping);
    }

    public function createSetterContext(
        DataDefinitionInterface $definition,
        array $params,
        Concrete $object,
        ImportMapping $mapping,
        array $dataRow,
        ImportDataSetInterface $dataSet,
        mixed $value,
    ): SetterContextInterface {
        return new SetterContext($definition, $params, [], $object, $mapping, $dataRow, $dataSet, $value);
    }

    public function createInterpreterContext(
        DataDefinitionInterface $definition,
        array $params,
        array $configuration,
        array $dataRow,
        ?ImportDataSetInterface $dataSet,
        Concrete $object,
        mixed $value,
        MappingInterface $mapping,
    ): InterpreterContextInterface {
        return new InterpreterContext($definition, $params, $configuration, $dataRow, $dataSet, $object, $value, $mapping);
    }

    public function createRunnerContext(
        DataDefinitionInterface $definition,
        array $params,
        ?array $dataRow,
        ?ImportDataSetInterface $dataSet,
        ?Concrete $object,
    ): RunnerContextInterface {
        return new RunnerContext($definition, $params, [], $dataRow, $dataSet, $object);
    }
}
