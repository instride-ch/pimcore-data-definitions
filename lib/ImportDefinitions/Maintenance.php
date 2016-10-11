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
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions;

use Symfony\Component\Finder\Finder;

class Maintenance
{
    /**
     * Arhive Import Log Files
     */
    public static function archiveLogFiles()
    {
        // rotate logs
        $finder = new Finder();
        $finder
            ->files()
            ->in(PIMCORE_LOG_DIRECTORY)
            ->name('import-definitions-*');

        $groupedFiles = [];

        foreach ($finder as $file) {
            $date = new \Zend_Date(filemtime($file));

            if(!$date->isToday()) {
                $niceDate = $date->get("yyyy-MM-dd");

                $groupedFiles[$niceDate][] = $file;
            }
        }

        foreach($groupedFiles as $date => $files) {
            $zip = new \ZipArchive();
            $filename = PIMCORE_LOG_DIRECTORY . "/import-definitions-$date-archive.zip";

            if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception("cannot open $filename");
            }

            foreach($files as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();

            foreach($files as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Clean up old Log Files
     */
    public function cleanupLogFiles()
    {
        // rotate logs
        $finder = new Finder();
        $finder
            ->files()
            ->in(PIMCORE_LOG_DIRECTORY)
            ->name('import-definitions-*.zip');

        foreach ($finder as $file) {
            if (filemtime($file) < (time()-(86400*30))) { // we keep the logs for 30 days
                unlink($file);
            }
        }
    }
}