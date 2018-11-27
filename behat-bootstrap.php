<?php

require __DIR__ . '/app/AppKernel.php';

define('TESTS_PATH', __DIR__);
define('PIMCORE_PROJECT_ROOT', __DIR__);
define('PIMCORE_TEST', true);
define('PIMCORE_KERNEL_CLASS', '\AppKernel');

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::boostrap();