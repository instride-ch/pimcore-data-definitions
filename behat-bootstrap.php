<?php

if (!defined('PIMCORE_PROJECT_ROOT')) {
    define(
        'PIMCORE_PROJECT_ROOT',
        getenv('PIMCORE_PROJECT_ROOT')
            ?: getenv('REDIRECT_PIMCORE_PROJECT_ROOT')
            ?: realpath(getcwd())
    );
}

define('PIMCORE_TEST', true);
define('PIMCORE_CLASS_DIRECTORY', __DIR__ . '/var/tmp/behat/var/classes');

error_reporting(E_ALL);

require_once __DIR__ .'/src/BehatKernel.php';

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::bootstrap();
