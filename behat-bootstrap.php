<?php
/**
 * Data Definitions.
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in LICENSE.md
 * which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
 */

if (!defined('PIMCORE_PROJECT_ROOT')) {
    define(
        'PIMCORE_PROJECT_ROOT',
        getenv('PIMCORE_PROJECT_ROOT')
            ?: getenv('REDIRECT_PIMCORE_PROJECT_ROOT')
            ?: realpath(getcwd())
    );
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ .'/src/BehatKernel.php';

if (file_exists(PIMCORE_PROJECT_ROOT.'/pimcore/config/bootstrap.php')) {
    require_once PIMCORE_PROJECT_ROOT.'/pimcore/config/bootstrap.php';
}
else {
    \Pimcore\Bootstrap::setProjectRoot();
    \Pimcore\Bootstrap::bootstrap();
}
