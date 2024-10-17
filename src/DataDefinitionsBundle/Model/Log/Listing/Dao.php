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

namespace Instride\Bundle\DataDefinitionsBundle\Model\Log\Listing;

use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Exception;
use Instride\Bundle\DataDefinitionsBundle\Model\Log;
use Pimcore\Model\Listing;
use Pimcore\Model\Listing\Dao\QueryBuilderHelperTrait;

class Dao extends Listing\Dao\AbstractDao
{
    use QueryBuilderHelperTrait;

    /**
     * @var string
     */
    protected $tableName = 'data_definitions_import_log';

    /**
     * Get tableName, either for localized or non-localized data.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Loads objects from the database.
     *
     * @return Log[]
     *
     * @throws Exception
     */
    public function load(): array
    {
        // load id's
        $list = $this->loadIdList();

        $objects = [];
        foreach ($list as $o_id) {
            if ($object = Log::getById($o_id)) {
                $objects[] = $object;
            }
        }

        $this->model->setObjects($objects);

        return $objects;
    }

    public function getQueryBuilder(...$columns): DoctrineQueryBuilder
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select(...$columns)->from($this->getTableName());

        $this->applyListingParametersToQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Loads a list for the specified parameters, returns an array of ids.
     *
     * @return array
     *
     * @throws Exception
     */
    public function loadIdList()
    {
        $queryBuilder = $this->getQueryBuilder(['id']);
        $assetIds = $this->db->fetchFirstColumn(
            (string) $queryBuilder,
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes(),
        );

        return array_map('intval', $assetIds);
    }

    /**
     * Get Count
     *
     * @throws Exception
     */
    public function getCount(): int
    {
        return (int) $this->db->fetchOne(
            'SELECT COUNT(*) as amount FROM ' . $this->getTableName() . $this->getCondition() . $this->getOffsetLimit(),
            [$this->model->getConditionVariables()],
        );
    }

    /**
     * Get Total Count.
     *
     * @throws Exception
     */
    public function getTotalCount(): int
    {
        return (int) $this->db->fetchOne(
            'SELECT COUNT(*) as amount FROM ' . $this->getTableName() . $this->getCondition(),
            [$this->model->getConditionVariables()],
        );
    }
}
