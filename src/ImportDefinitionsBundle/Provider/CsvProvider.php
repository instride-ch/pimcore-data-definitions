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

namespace ImportDefinitionsBundle\Provider;

use ImportDefinitionsBundle\Model\Mapping\FromColumn;

class CsvProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function testData($configuration): bool
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getColumns($configuration): array
    {
        $csvHeaders = $configuration['csvHeaders'];
        $csvExample = $configuration['csvExample'];
        $delimiter = $configuration['delimiter'];
        $enclosure = $configuration['enclosure'];

        $returnHeaders = [];
        $rows = str_getcsv($csvHeaders ?: $csvExample, "\n"); //parse the rows

        if (\count($rows) > 0) {
            $headerRow = $rows[0];

            $headers = str_getcsv($headerRow, $delimiter, $enclosure ?: \chr(8));

            if (\count($headers) > 0) {
                //First line are the headers
                foreach ($headers as $header) {
                    if (!$header) {
                        continue;
                    }

                    $headerObj = new FromColumn();
                    $headerObj->setIdentifier($header);
                    $headerObj->setLabel($header);

                    $returnHeaders[] = $headerObj;
                }
            }
        }

        return $returnHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($configuration, $definition, $params, $filter = null): array
    {
        $csvHeaders = $configuration['csvHeaders'];
        $delimiter = $configuration['delimiter'];
        $enclosure = $configuration['enclosure'];

        $file = sprintf('%s/%s', PIMCORE_PROJECT_ROOT, $params['file']);

        $columnMapping = [];

        if ($csvHeaders) {
            $columnMapping = $this->getColumns($configuration);

            foreach ($columnMapping as &$header) {
                $header = $header->getIdentifier();
            }

            unset($header);
        }

        $data = [];

        $row = 0;
        if (($handle = fopen($file, 'rb')) !== false) {
            while (($csvData = fgetcsv($handle, 1000, $delimiter, $enclosure ?: \chr(8))) !== false) {
                $num = \count($csvData);

                // Make Column Mapping
                if ($row === 0 && !$csvHeaders) {
                    for ($c = 0; $c < $num; $c++) {
                        $columnMapping[] = $csvData[$c];
                    }
                } else {
                    $mappedData = [];

                    foreach ($csvData as $index => $col) {
                        $mappedData[$columnMapping[$index]] = $col;
                    }

                    $data[] = $mappedData;
                }

                $row++;
            }

            fclose($handle);
        }

        return $data;
    }
}