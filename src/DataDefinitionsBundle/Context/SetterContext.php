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
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;

class SetterContext implements SetterContextInterface
{
    public function __construct(
        protected DataDefinitionInterface $definition,
        protected array $params,
        protected Concrete $object,
        protected ImportMapping $mapping,
        protected array $dataRow,
        protected ImportDataSetInterface $dataSet,
        protected mixed $value,
    ) {
    }

    public function getDefinition(): DataDefinitionInterface
    {
        return $this->definition;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getObject(): Concrete
    {
        return $this->object;
    }

    public function getImportMapping(): ImportMapping
    {
        return $this->mapping;
    }

    public function getDataRow(): array
    {
        return $this->dataRow;
    }

    public function getDataSet(): ImportDataSetInterface
    {
        return $this->dataSet;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
