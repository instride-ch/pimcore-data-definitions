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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Wvision\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Model\Asset;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcoreAssetContext implements Context
{
    private $sharedStorage;
    private $kernel;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        KernelInterface $kernel
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->kernel = $kernel;
    }

    /**
     * @Given /^there is a asset with bundle file "([^"]+)"$/
     * @Given /^there is a asset with bundle file "([^"]+)" at path "([^"]+)"$/
     */
    public function thereIsAAssetWithBundleFile($bundleFile, $parentPath = null)
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
