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

namespace ImportDefinitionsBundle\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use ImportDefinitionsBundle\Installer;

final class IMSetupContext implements Context
{
    /**
     * @var bool
     */
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
}