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

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle;

use Pimcore;
use Pimcore\Console\Application;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Symfony\Component\Console\Input\ArrayInput;
use Wvision\Bundle\DataDefinitionsBundle\Migrations\Version20190731104917;

class Installer extends SettingsStoreAwareInstaller
{
    public function getLastMigrationVersionClassName(): ?string
    {
        return Version20190731104917::class;
    }

    public function install(): void
    {
        $kernel = Pimcore::getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:resources:install'];
        $options = array_merge($options, ['--no-interaction' => true, '--application-name data_definitions']);
        $application->run(new ArrayInput($options));

        parent::install();
    }

    public function uninstall(): void
    {
        parent::uninstall();
    }
}
