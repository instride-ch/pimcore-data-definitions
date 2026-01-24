<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in LICENSE.md
 * which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\DriverManager;

final class PimcoreSetupContext implements Context
{
    private static $pimcoreSetupDone = false;

    /**
     * @BeforeSuite
     */
    public static function setupPimcore(): void
    {
        if (getenv('IM_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$pimcoreSetupDone) {
            return;
        }

        $connection = \Pimcore::getContainer()->get('database_connection');

        if (null === $connection) {
            throw new \Exception('Database connection not found');
        }

        $dbName = $connection->getParams()['dbname'];
        $params = $connection->getParams();
        $config = $connection->getConfiguration();

        unset($params['url'], $params['dbname']);

        // use a dedicated setup connection as the framework connection is bound to the DB and will
        // fail if the DB doesn't exist
        $setupConnection = DriverManager::getConnection($params, $config);
        $schemaManager = $setupConnection->getSchemaManager();

        $databases = $schemaManager->listDatabases();
        if (in_array($dbName, $databases)) {
            $schemaManager->dropDatabase($connection->quoteIdentifier($dbName));
        }

        $schemaManager->createDatabase($connection->quoteIdentifier($dbName));

        if (!$connection->isConnected()) {
            $connection->connect();
        }

        $installer = new \Pimcore\Bundle\InstallBundle\Installer(
            \Pimcore::getContainer()->get('monolog.logger.pimcore'),
            \Pimcore::getContainer()->get('event_dispatcher'),
        );
        
        $installer->setupDatabase($connection, [
            'username' => 'admin',
            'password' => 'coreshop',
        ]);


        static::$pimcoreSetupDone = true;
    }
}
