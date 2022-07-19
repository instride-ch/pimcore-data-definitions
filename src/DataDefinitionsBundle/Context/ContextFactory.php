<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Context;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;

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
