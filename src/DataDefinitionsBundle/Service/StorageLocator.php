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

namespace Instride\Bundle\DataDefinitionsBundle\Service;

use League\Flysystem\FilesystemOperator;
use Psr\Container\ContainerInterface;

class StorageLocator
{
    private ContainerInterface $locator;

    public function __construct(
        ContainerInterface $locator,
    ) {
        $this->locator = $locator;
    }

    public function getStorage(string $name): FilesystemOperator
    {
        return $this->locator->get($name);
    }
}
