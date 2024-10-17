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

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

use Doctrine\DBAL\Connection;
use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use function is_object;

abstract class AbstractSqlProvider implements ImportProviderInterface
{
    abstract protected function getDb(array $configuration): Connection;

    public function testData(array $configuration): bool
    {
        return is_object($this->getDb($configuration));
    }

    public function getColumns(array $configuration): array
    {
        $db = $this->getDb($configuration);
        $query = $db->executeQuery($configuration['query']);
        $data = $query->fetchAssociative();
        $columns = [];
        $returnColumns = [];

        if (count($data) > 0) {
            // there is at least one row - we can grab columns from it
            $columns = array_keys((array) $data);
        }

        foreach ($columns as $col) {
            $returnCol = new FromColumn();
            $returnCol->setIdentifier($col);
            $returnCol->setLabel($col);

            $returnColumns[] = $returnCol;
        }

        return $returnColumns;
    }

    public function getData(
        array $configuration,
        ImportDefinitionInterface $definition,
        array $params,
        FilterInterface $filter = null,
    ): ImportDataSetInterface {
        $db = $this->getDb($configuration);

        return new ArrayImportDataSet($db->fetchAllAssociative($configuration['query']));
    }
}
