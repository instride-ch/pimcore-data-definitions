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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
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
