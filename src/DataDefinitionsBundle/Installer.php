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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Console\Application;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Symfony\Component\Console\Input\ArrayInput;

class Installer extends MigrationInstaller
{
    protected function beforeInstallMigration()
    {
        $kernel = \Pimcore::getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:resources:install'];
        $options = array_merge($options, ['--no-interaction' => true, '--application-name data_definitions']);
        $application->run(new ArrayInput($options));
    }

    public function migrateInstall(Schema $schema, Version $version)
    {
    }

    public function migrateUninstall(Schema $schema, Version $version)
    {
    }
}

