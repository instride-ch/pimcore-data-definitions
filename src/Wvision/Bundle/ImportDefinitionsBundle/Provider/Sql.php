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
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $database;

    /**
     * @var string
     */
    public $adapter;

    /**
     * @var string
     */
    public $port;

    /**
     * @var string
     */
    public $query;

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDb()
    {
        $config = new Configuration();
        $connectionParams = array(
            'dbname' => $this->getDatabase(),
            'user' => $this->getUsername(),
            'password' => $this->getPassword(),
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'driver' => 'pdo_mysql',
        );
        $conn = DriverManager::getConnection($connectionParams, $config);

        return $conn;
    }
    
    /**
     * {@inheritdoc}
     */
    public function testData()
    {
        return is_object($this->getDb());
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $db = $this->getDb();
        $query = $db->query($this->getQuery());
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
    public function getData($definition, $params, $filter = null)
    {
        $db = $this->getDb();

        $data = $db->fetchAll($this->getQuery());

        return $data;
    }
}
