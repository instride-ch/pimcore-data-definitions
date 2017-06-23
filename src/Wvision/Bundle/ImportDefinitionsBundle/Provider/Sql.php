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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\ImportDefinitionsBundle\Provider;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping\FromColumn;

class Sql implements ProviderInterface
{
    /**
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDb($configuration)
    {
        $config = new Configuration();
        $connectionParams = array(
            'dbname' => $configuration['database'],
            'user' => $configuration['username'],
            'password' => $configuration['password'],
            'host' => $configuration['host'],
            'port' => $configuration['port'],
            'driver' => 'pdo_mysql',
        );
        $conn = DriverManager::getConnection($connectionParams, $config);

        return $conn;
    }
    
    /**
     * {@inheritdoc}
     */
    public function testData($configuration)
    {
        return is_object($this->getDb($configuration));
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($configuration)
    {
        $db = $this->getDb($configuration);
        $query = $db->query($configuration['query']);
        $data = $query->fetchAll();
        $columns = [];
        $returnColumns = [];

        if (isset($data[0])) {
            // there is at least one row - we can grab columns from it
            $columns = array_keys((array)$data[0]);
        } else {
            $columns = [];
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

        $data = $db->fetchAll($configuration['query']);

        return $data;
    }
}
