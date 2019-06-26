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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace WVision\Bundle\DataDefinitionsBundle\Provider;

use Doctrine\DBAL\Connection;
use WVision\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;

abstract class AbstractSqlProvider implements ProviderInterface
{
    /**
     * @param $configuration
     * @return Connection
     */
    abstract protected function getDb($configuration);

    /**
     * {@inheritdoc}
     */
    public function testData($configuration)
    {
        return \is_object($this->getDb($configuration));
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getColumns($configuration)
    {
        $db = $this->getDb($configuration);
        $query = $db->query($configuration['query']);
        $data = $query->fetch();
        $columns = [];
        $returnColumns = [];

        if (isset($data)) {
            // there is at least one row - we can grab columns from it
            $columns = array_keys((array)$data);
        }

        foreach ($columns as $col) {
            $returnCol = new FromColumn();
            $returnCol->setIdentifier($col);
            $returnCol->setLabel($col);

            $returnColumns[] = $returnCol;
        }

        return $returnColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($configuration, $definition, $params, $filter = null)
    {
        $db = $this->getDb($configuration);

        return $db->fetchAll($configuration['query']);
    }
}

class_alias(AbstractSqlProvider::class, 'ImportDefinitionsBundle\Provider\AbstractSqlProvider');
