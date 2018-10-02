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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\ProcessManager;

use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\Report\ReportInterface;

class ImportDefinitionsReport implements ReportInterface
{
    const EVENT_TOTAL = 'import_definition.total: ';
    const EVENT_STATUS = 'import_definition.status: ';
    const EVENT_PROGRESS = 'import_definition.progress: ';
    const EVENT_FINISHED = 'import_definition.finished: ';
    const EVENT_STATUS_ERROR = self::EVENT_STATUS . 'Error: ';
    const EVENT_STATUS_IMPORT_NEW = self::EVENT_STATUS . 'Import Object new';
    const EVENT_STATUS_IMPORT_EXISTING = self::EVENT_STATUS . 'Import Object';
    const EVENT_STATUS_IGNORE_NEW = self::EVENT_STATUS . 'Ignoring new Object';
    const EVENT_STATUS_IGNORE_EXISTING = self::EVENT_STATUS . 'Ignoring existing Object';
    const EVENT_STATUS_IGNORE_FILTERED = self::EVENT_STATUS . 'Filtered Object';

    const CHECKS = [
        [
            'text' => self::EVENT_STATUS_IMPORT_NEW,
            'attr' => 'new',
        ],
        [
            'text' => self::EVENT_STATUS_IMPORT_EXISTING,
            'attr' => 'existing',
        ],
        [
            'text' => self::EVENT_STATUS_IGNORE_NEW,
            'attr' => 'ignore_new',
        ],
        [
            'text' => self::EVENT_STATUS_IGNORE_EXISTING,
            'attr' => 'ignore_existing',
        ],
        [
            'text' => self::EVENT_STATUS_IGNORE_FILTERED,
            'attr' => 'ignore_filtered',
        ],
    ];


    public function generateReport(ProcessInterface $process, $log)
    {
        $result = $this->doReport($log);

        $importedNew = 0;
        $importedExisting = 0;
        $skippedNew = 0;
        $skippedExisting = 0;
        $skippedFiltered = 0;
        $errorsCnt = 0;
        $errors = [];
        $total = $result['total'];
        $cnt = $result['currentObject'];

        for ($iter = 0; $iter <= $cnt; ++$iter) {
            if (isset($result['productStatus'][$iter])) {
                $status = $result['productStatus'][$iter];

                if (isset($status['error'])) {
                    $errors[] = '<b>Line '.($iter + 1).'. Error: </b>'.$status['error'];
                    $errorsCnt++;
                } elseif (isset($status['ignore_filtered'])) {
                    $skippedFiltered++;
                } elseif (isset($status['ignore_new'])) {
                    $skippedNew++;
                } elseif (isset($status['ignore_existing'])) {
                    $skippedExisting++;
                } elseif (isset($status['new'])) {
                    $importedNew++;
                } elseif (isset($status['existing'])) {
                    $importedExisting++;
                }
            }
        }

        $items = [];
        $items[] = 'Total lines processed: '.$total;
        if ($importedNew) {
            $items[] = 'Imported new objects: '.$importedNew;
        }

        if ($importedExisting) {
            $items[] = 'Updates: '.$importedExisting;
        }

        if ($skippedNew) {
            $items[] = 'Skipped new: '.$skippedNew;
        }
        if ($skippedExisting) {
            $items[] = 'Skipped existing: '.$skippedExisting;
        }
        if ($skippedFiltered) {
            $items[] = 'Filtered: '.$skippedFiltered;
        }
        $items[] = 'Errors count: '.$errorsCnt;

        if (count($errors)) {
            $items[] = 'Errors: ';
            $items = array_merge($items, $errors);
        }

        return implode('<br />', $items);
    }

    protected function doReport($log)
    {
        $lines = explode(PHP_EOL, $log);
        $result = [
            'currentObject' => 0,
            'productStatus' => [],
        ];

        foreach ($lines as $line) {
            $this->processLine($line, $result);
        }

        return $result;
    }

    /**
     * @param $line
     * @param $result
     */
    protected function processLine($line, &$result)
    {
        if ($this->checkForProgress($line, $result)) {
            return;
        }


        if ($this->checkForTotal($line, $result)) {
            return;
        }

        if ($this->checkForError($line, $result)) {
            return;
        }

        $this->processChecks($line, $result);
    }

    /**
     * @param $line
     * @param $result
     * @return bool
     */
    protected function checkForProgress($line, &$result)
    {
        if (false !== strpos($line, self::EVENT_PROGRESS)) {
            $result['currentObject'] = $result['currentObject'] + 1;

            return true;
        }

        return false;
    }

    /**
     * @param $line
     * @param $result
     * @return bool
     */
    protected function checkForTotal($line, &$result)
    {
        $pos = strpos($line, self::EVENT_TOTAL);
        if ($pos) {
            $total = substr($line, $pos + strlen(self::EVENT_TOTAL));
            $result['total'] = intval($total);

            return true;
        }

        return false;
    }

    /**
     * @param $line
     * @param $result
     * @return bool
     */
    protected function checkForError($line, &$result)
    {
        $pos = strpos($line, self::EVENT_STATUS_ERROR);
        if (false !== $pos) {
            $result[$result['currentObject']]['error'] = substr($line, $pos + strlen(self::EVENT_STATUS_ERROR));

            return true;
        }

        return false;
    }

    /**
     * @param $line
     * @param $result
     */
    protected function processChecks($line, &$result)
    {
        foreach (self::CHECKS as $check) {
            $pos = strpos($line, $check['text']);
            if (false !== $pos) {
                $result['productStatus'][$result['currentObject']][$check['attr']] = true;
            }
        }
    }
}