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

namespace ImportDefinitionsBundle\Provider;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ExternalSqlProvider extends AbstractSqlProvider
{
    /**
     * @param $configuration
     * @return Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getDb($configuration)
    {
        $config = new Configuration();
        $connectionParams = [
            'dbname' => $configuration['database'],
            'user' => $configuration['username'],
            'password' => $configuration['password'],
            'host' => $configuration['host'],
            'port' => $configuration['port'],
            'driver' => 'pdo_mysql',
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }
}
