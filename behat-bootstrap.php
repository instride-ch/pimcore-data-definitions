<?php

require __DIR__ . '/src/Kernel.php';

define('TESTS_PATH', __DIR__);
define('PIMCORE_PROJECT_ROOT', __DIR__);
define('PIMCORE_KERNEL_CLASS', '\AppKernel');

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::bootstrap();
