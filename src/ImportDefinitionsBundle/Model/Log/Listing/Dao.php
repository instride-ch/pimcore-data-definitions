<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Model\Log\Listing;

use Pimcore\Db\ZendCompatibility\Expression;
use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\Model\Listing;
use ImportDefinitionsBundle\Model\Log;

class Dao extends Listing\Dao\AbstractDao
{
    /**
     * @var string
     */
    protected $tableName = 'import_definitions_log';

    /**
     * Get tableName, either for localized or non-localized data.
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return QueryBuilder
     * @throws \Exception
     */
    public function getQuery()
    {
        // init
        $select = $this->db->select();

        // create base
        $field = sprintf('%s.id', $this->getTableName());
        $select->from(
            [$this->getTableName()], [
                new Expression(sprintf('SQL_CALC_FOUND_ROWS %s as id', $field)),
            ]
        );

        // add condition
        $this->addConditions($select);

        // group by
        $this->addGroupBy($select);

        // order
        $this->addOrder($select);

        // limit
        $this->addLimit($select);

        return $select;
    }

    /**
     * Loads objects from the database.
     *
     * @return Log[]
     * @throws \Exception
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

    /**
     * Loads a list for the specified parameters, returns an array of ids.
     *
     * @return array
     * @throws \Exception
     */
    public function loadIdList()
    {
        try {
            $query = $this->getQuery();
            $objectIds = $this->db->fetchCol($query, $this->model->getConditionVariables());
            $this->totalCount = (int) $this->db->fetchOne('SELECT FOUND_ROWS()');

            return $objectIds;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Count
     *
     * @return int
     * @throws \Exception
     */
    public function getCount()
    {
        $amount = (int) $this->db->fetchOne('SELECT COUNT(*) as amount FROM ' . $this->getTableName() . $this->getCondition() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        return $amount;
    }

    /**
     * Get Total Count.
     *
     * @return int
     * @throws \Exception
     */
    public function getTotalCount()
    {
        $amount = (int) $this->db->fetchOne('SELECT COUNT(*) as amount FROM ' . $this->getTableName() . $this->getCondition(), $this->model->getConditionVariables());

        return $amount;
    }
}
