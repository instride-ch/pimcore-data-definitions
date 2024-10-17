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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ExternalSqlProvider extends AbstractSqlProvider
{
    protected function getDb(array $configuration): Connection
    {
        $config = new Configuration();
        $connectionParams = [
            'dbname' => $configuration['database'],
            'user' => $configuration['username'],
            'password' => $configuration['password'],
            'host' => $configuration['host'],
            'port' => $configuration['port'],
            'driver' => $configuration['adapter'],
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }
}
