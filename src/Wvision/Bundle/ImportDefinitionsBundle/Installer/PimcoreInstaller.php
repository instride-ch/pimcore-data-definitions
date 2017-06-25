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

namespace Wvision\Bundle\ImportDefinitionsBundle\Installer;

use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class PimcoreInstaller extends AbstractInstaller
{
    /**
     * {@inheritdoc}
     */
    public function install()
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
    public function uninstall()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled()
    {
        $db = Db::get();
        return $db->getSchemaManager()->tablesExist(['import_definitions_log']);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeInstalled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUninstalled()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUpdated()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        //InstallHelper::runDoctrineOrmSchemaUpdate();
    }
}
