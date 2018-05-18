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

namespace ImportDefinitionsBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class Installer extends MigrationInstaller
{
    /**
     * @throws \Exception
     */
    protected function beforeInstallMigration()
    {
        $kernel = \Pimcore::getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:resources:install'];
        $options = array_merge($options, ['--no-interaction' => true, '--application-name import_definitions']);
        $application->run(new ArrayInput($options));
    }

    /**
     * {@inheritdoc}
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
    }
}
