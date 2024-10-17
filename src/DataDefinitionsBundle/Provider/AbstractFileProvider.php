<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

use Instride\Bundle\DataDefinitionsBundle\Service\StorageLocator;
use Pimcore\File;
use Pimcore\Helper\LongRunningHelper;
use Pimcore\Model\Asset;

abstract class AbstractFileProvider
{
    public function __construct(
        protected StorageLocator $storageLocator,
        protected LongRunningHelper $longRunningHelper,
    ) {
    }

    protected function getFile(array $params): string
    {
//        if (!str_starts_with($file, '/')) {
//            $file = sprintf('%s/%s', PIMCORE_PROJECT_ROOT, $file);
//        }

        if (isset($params['asset'])) {
            $asset = Asset::getByPath($params['asset']);

            if (!$asset) {
                throw new \RuntimeException(sprintf('Asset "%s" not found', $params['asset']));
            }

            return $this->createTemporaryFileFromStream($asset->getStream());
        }

        if (isset($params['storage'], $params['file'])) {
            $storage = $this->storageLocator->getStorage($params['storage']);

            if (!$storage->fileExists($params['file'])) {
                throw new \RuntimeException(sprintf('File "%s" in Storage "%s" not found', $params['file'], $params['storage']));
            }

            return $this->createTemporaryFileFromStream($storage->readStream($params['file']));
        }

        if (isset($params['file'])) {
            return $params['file'];
        }

        throw new \RuntimeException('No file or asset given');
    }

    protected function createTemporaryFileFromStream($stream)
    {
        if (is_string($stream)) {
            $src = fopen($stream, 'rb');
            $fileExtension = pathinfo($stream, \PATHINFO_EXTENSION);
        } else {
            $src = $stream;
            $streamMeta = stream_get_meta_data($src);
            $fileExtension = pathinfo($streamMeta['uri'], \PATHINFO_EXTENSION);
        }

        $tmpFilePath = File::getLocalTempFilePath($fileExtension);

        $dest = fopen($tmpFilePath, 'wb', false, File::getContext());
        if (!$dest) {
            throw new \Exception(sprintf('Unable to create temporary file in %s', $tmpFilePath));
        }

        stream_copy_to_stream($src, $dest);
        fclose($dest);

        $this->longRunningHelper->addTmpFilePath($tmpFilePath);
        register_shutdown_function(static function () use ($tmpFilePath) {
            @unlink($tmpFilePath);
        });

        return $tmpFilePath;
    }
}
