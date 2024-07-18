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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
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
