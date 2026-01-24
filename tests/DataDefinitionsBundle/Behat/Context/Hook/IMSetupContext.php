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
use Instride\Bundle\DataDefinitionsBundle\Installer;
use Pimcore\Db\PhpArrayFileTable;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinition;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;

final class IMSetupContext implements Context
{
    private static $setupDone = false;

    /**
     * @BeforeSuite
     */
    public static function setupImportDefinitions()
    {
        if (getenv('IM_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$setupDone) {
            return;
        }

        $installer = \Pimcore::getContainer()->get(Installer::class);
        $installer->install();

        static::$setupDone = true;
    }

    /**
     * @BeforeScenario
     */
    public function purgeDefinitions()
    {
        $importDefinitions = new ImportDefinition\Listing();

        foreach ($importDefinitions->getObjects() as $definition) {
            $definition->delete();
        }

        $exportDefinitions = new ExportDefinition\Listing();

        foreach ($exportDefinitions->getObjects() as $definition) {
            $definition->delete();
        }
//
//        if (file_exists(PIMCORE_CONFIGURATION_DIRECTORY.'/importdefinitions.php')) {
//            unlink(PIMCORE_CONFIGURATION_DIRECTORY.'/importdefinitions.php');
//        }
//
//        if (file_exists(PIMCORE_CONFIGURATION_DIRECTORY.'/exportdefinitions.php')) {
//            unlink(PIMCORE_CONFIGURATION_DIRECTORY.'/exportdefinitions.php');
//        }
//
//        $obj = new PhpArrayFileTable();
//        $refObject = new \ReflectionObject($obj);
//        $refProperty = $refObject->getProperty('tables');
//        $refProperty->setAccessible(true);
//        $refProperty->setValue(null, []);
    }
}
