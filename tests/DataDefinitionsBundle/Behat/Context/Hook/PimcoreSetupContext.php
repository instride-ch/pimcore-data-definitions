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

namespace WVision\Bundle\DataDefinitionsBundle\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\DriverManager;

final class PimcoreSetupContext implements Context
{
    private static $pimcoreSetupDone = false;

    /**
     * @BeforeSuite
     */
    public static function setupPimcore()
    {
        if (getenv('IM_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$pimcoreSetupDone) {
            return;
        }

        $connection = \Pimcore::getContainer()->get('database_connection');

        $dbName = $connection->getParams()['dbname'];
        $params = $connection->getParams();
        $config = $connection->getConfiguration();

        unset($params['url']);
        unset($params['dbname']);

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

        //Prior 5.5
        if (@class_exists('\Pimcore\Model\Tool\Setup')) {
            $setup = new \Pimcore\Model\Tool\Setup();
            $setup->database();

            $setup->contents(
                [
                    'username' => 'admin',
                    'password' => microtime(),
                ]
            );
        }
        else {
            $installer = new \Pimcore\Bundle\InstallBundle\Installer(
                \Pimcore::getContainer()->get('monolog.logger.pimcore'),
                \Pimcore::getContainer()->get('event_dispatcher')
            );

            $installer->setupDatabase([
                'username' => 'admin',
                'password' => microtime(),
            ]);
        }

        static::$pimcoreSetupDone = true;
    }
}
