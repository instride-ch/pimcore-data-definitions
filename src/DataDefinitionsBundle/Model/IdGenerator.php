<?php

namespace Instride\Bundle\DataDefinitionsBundle\Model;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;

trait IdGenerator
{
    private string $mainPath = 'var/config';

    /**
     * @throws FilesystemException
     */
    private function getSuggestedId(string $definitionPath): int
    {
        // Create an adapter and a Filesystem instance
        $adapter = new LocalFilesystemAdapter(
            sprintf('%s/%s/%s', PIMCORE_PROJECT_ROOT, $this->mainPath, $definitionPath)
        );

        $filesystem = new Filesystem($adapter);

        // Get the list of files from the directory
        $contents = $filesystem->listContents('/', true);

        $maxNumber = null;
        foreach ($contents as $file) {
            if ($file->isFile() && preg_match('/^(\d+)\.yaml$/', $file->path(), $matches)) {
                $number = (int)$matches[1];
                if ($maxNumber === null || $number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        return $maxNumber + 1;
    }

}
