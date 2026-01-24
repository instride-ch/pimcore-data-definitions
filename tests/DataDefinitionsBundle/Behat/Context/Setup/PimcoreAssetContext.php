<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in LICENSE.md
 * which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Instride\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Model\Asset;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcoreAssetContext implements Context
{

    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly KernelInterface $kernel
    ) {
    }

    /**
     * @Given /^there is a asset with bundle file "([^"]+)"$/
     * @Given /^there is a asset with bundle file "([^"]+)" at path "([^"]+)"$/
     */
    public function thereIsAAssetWithBundleFile(string $bundleFile, ?string $parentPath = null): void
    {
        $path = $this->kernel->locateResource($bundleFile);
        $parentId = 1;

        if (null !== $parentPath) {
            $parentId = Asset\Service::createFolderByPath($parentPath)->getId();
        }

        Asset::create($parentId, [
            'filename' => basename($path),
            'sourcePath' => $path
        ]);
    }
}
