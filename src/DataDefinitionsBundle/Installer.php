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

namespace Instride\Bundle\DataDefinitionsBundle;

use Pimcore;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Symfony\Component\Console\Input\ArrayInput;

class Installer extends SettingsStoreAwareInstaller
{
    public function install(): void
    {
        $kernel = Pimcore::getKernel();
        $application = new Pimcore\Console\Application($kernel);
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
