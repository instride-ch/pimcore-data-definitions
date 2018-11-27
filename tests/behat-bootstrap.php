<?php

if (!defined('PIMCORE_PROJECT_ROOT')) {
    define(
        'PIMCORE_PROJECT_ROOT',
        getenv('PIMCORE_PROJECT_ROOT')
            ?: getenv('REDIRECT_PIMCORE_PROJECT_ROOT')
            ?: realpath(getcwd())
    );
}

if (!defined('TESTS_PATH')) {
    define('TESTS_PATH', __DIR__);
}

define('PIMCORE_CLASS_DIRECTORY', __DIR__ . '/tmp/var/classes');
define('PIMCORE_TEST', true);

require_once __DIR__ .'/../app/TestAppKernel.php';

if (file_exists(PIMCORE_PROJECT_ROOT.'/pimcore/config/bootstrap.php')) {
    require_once PIMCORE_PROJECT_ROOT.'/pimcore/config/bootstrap.php';
}
else {
    \Pimcore\Bootstrap::setProjectRoot();
    \Pimcore\Bootstrap::boostrap();
}
