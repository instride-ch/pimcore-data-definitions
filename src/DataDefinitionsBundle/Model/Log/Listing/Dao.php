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

namespace Wvision\Bundle\DataDefinitionsBundle\Model\Log\Listing;

use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Exception;
use Pimcore\Model\Listing;
use Pimcore\Model\Listing\Dao\QueryBuilderHelperTrait;
use Wvision\Bundle\DataDefinitionsBundle\Model\Log;

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
     * @throws Exception
     */
    public function load()
    {
        // load id's
        $list = $this->loadIdList();

        $objects = array();
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
     * @throws Exception
     */
    public function loadIdList()
    {
        $queryBuilder = $this->getQueryBuilder(['id']);
        $assetIds = $this->db->fetchCol(
            (string)$queryBuilder,
            $this->model->getConditionVariables(),
            $this->model->getConditionVariableTypes()
        );

        return array_map('intval', $assetIds);
    }


    /**
     * Get Count
     *
     * @return int
     * @throws Exception
     */
    public function getCount(): int
    {
        return (int)$this->db->fetchOne(
            'SELECT COUNT(*) as amount FROM '.$this->getTableName().$this->getCondition().$this->getOffsetLimit(),
            $this->model->getConditionVariables()
        );
    }

    /**
     * Get Total Count.
     *
     * @return int
     * @throws Exception
     */
    public function getTotalCount(): int
    {
        return (int)$this->db->fetchOne(
            'SELECT COUNT(*) as amount FROM '.$this->getTableName().$this->getCondition(),
            $this->model->getConditionVariables()
        );
    }
}
