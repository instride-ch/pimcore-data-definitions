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

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\Report\ReportInterface;

class ImportDefinitionsReport implements ReportInterface
{
    public const EVENT_TOTAL = 'data_definitions.import.total: ';

    public const EVENT_STATUS = 'data_definitions.import.status: ';

    public const EVENT_PROGRESS = 'data_definitions.import.progress: ';

    public const EVENT_FINISHED = 'data_definitions.import.finished: ';

    public const EVENT_STATUS_ERROR = self::EVENT_STATUS . 'Error: ';

    public const EVENT_STATUS_IMPORT_NEW = self::EVENT_STATUS . 'Import Object new';

    public const EVENT_STATUS_IMPORT_EXISTING = self::EVENT_STATUS . 'Import Object';

    public const EVENT_STATUS_IGNORE_NEW = self::EVENT_STATUS . 'Ignoring new Object';

    public const EVENT_STATUS_IGNORE_EXISTING = self::EVENT_STATUS . 'Ignoring existing Object';

    public const EVENT_STATUS_IGNORE_FILTERED = self::EVENT_STATUS . 'Filtered Object';

    public const CHECKS = [
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

    public function generateReport(ProcessInterface $process, string $log): string
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
                    $errors[] = '<b>Line ' . ($iter + 1) . '. Error: </b>' . $status['error'];
                    ++$errorsCnt;
                } elseif (isset($status['ignore_filtered'])) {
                    ++$skippedFiltered;
                } elseif (isset($status['ignore_new'])) {
                    ++$skippedNew;
                } elseif (isset($status['ignore_existing'])) {
                    ++$skippedExisting;
                } elseif (isset($status['new'])) {
                    ++$importedNew;
                } elseif (isset($status['existing'])) {
                    ++$importedExisting;
                }
            }
        }

        $items = [];
        $items[] = 'Total lines processed: ' . $total;
        if ($importedNew) {
            $items[] = 'Imported new objects: ' . $importedNew;
        }

        if ($importedExisting) {
            $items[] = 'Updates: ' . $importedExisting;
        }

        if ($skippedNew) {
            $items[] = 'Skipped new: ' . $skippedNew;
        }
        if ($skippedExisting) {
            $items[] = 'Skipped existing: ' . $skippedExisting;
        }
        if ($skippedFiltered) {
            $items[] = 'Filtered: ' . $skippedFiltered;
        }
        $items[] = 'Errors count: ' . $errorsCnt;

        if (count($errors)) {
            $items[] = 'Errors: ';
            $items = array_merge($items, $errors);
        }

        return implode('<br />', $items);
    }

    protected function doReport($log)
    {
        $lines = explode(\PHP_EOL, $log);
        $result = [
            'currentObject' => 0,
            'productStatus' => [],
        ];

        foreach ($lines as $line) {
            $this->processLine($line, $result);
        }

        return $result;
    }

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

    protected function checkForProgress($line, &$result): bool
    {
        if (str_contains($line, self::EVENT_PROGRESS)) {
            $result['currentObject'] = $result['currentObject'] + 1;

            return true;
        }

        return false;
    }

    protected function checkForTotal($line, &$result): bool
    {
        $pos = strpos($line, self::EVENT_TOTAL);
        if ($pos) {
            $total = substr($line, $pos + strlen(self::EVENT_TOTAL));
            $result['total'] = (int) $total;

            return true;
        }

        return false;
    }

    protected function checkForError($line, &$result): bool
    {
        $pos = strpos($line, self::EVENT_STATUS_ERROR);
        if (false !== $pos) {
            $result['productStatus'][$result['currentObject']]['error'] = substr(
                $line,
                $pos + strlen(self::EVENT_STATUS_ERROR),
            );

            return true;
        }

        return false;
    }

    protected function processChecks($line, &$result): void
    {
        foreach (self::CHECKS as $check) {
            $pos = strpos($line, $check['text']);
            if (false !== $pos) {
                $result['productStatus'][$result['currentObject']][$check['attr']] = true;
            }
        }
    }
}
