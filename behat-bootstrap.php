<?php

define('TESTS_PATH', __DIR__);
define('PIMCORE_PROJECT_ROOT', __DIR__);

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::bootstrap();
