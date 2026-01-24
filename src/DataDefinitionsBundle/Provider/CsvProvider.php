<?php

declare(strict_types=1);

/*
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

use function chr;
use function count;
use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class CsvProvider extends AbstractFileProvider implements ImportProviderInterface, ExportProviderInterface
{
    private array $exportData = [];

    public function testData(array $configuration): bool
    {
        return true;
    }

    public function getColumns(array $configuration): array
    {
        $csvHeaders = (string) $configuration['csvHeaders'];
        $csvExample = $configuration['csvExample'];
        $delimiter = $configuration['delimiter'];
        $enclosure = $configuration['enclosure'];

        $returnHeaders = [];
        $csv = $csvHeaders ?: $csvExample;
        $rows = str_getcsv($csv ?: '', "\n"); //parse the rows

        if (count($rows) > 0) {
            $headerRow = $rows[0];

            $headers = str_getcsv($headerRow ?: '', $delimiter ?? ',', $enclosure ?: chr(8));

            if (count($headers) > 0) {
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

    public function getData(
        array $configuration,
        ImportDefinitionInterface $definition,
        array $params,
        FilterInterface $filter = null,
    ): ImportDataSetInterface {
        $csvHeaders = $configuration['csvHeaders'];
        $delimiter = $configuration['delimiter'];
        $enclosure = $configuration['enclosure'];

        $offset = $params['offset'] ?? null;
        $limit = $params['limit'] ?? null;

        $file = $this->getFile($params);

        $csv = Reader::createFromPath($file, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setEnclosure($enclosure);

        if ($csvHeaders) {
            $headers = array_map(function (FromColumn $column) {
                return $column->getIdentifier();
            }, $this->getColumns($configuration));

            $writer = Writer::createFromString('');

            $stmt = new Statement();
            $records = $stmt->process($csv);

            $writer->insertOne($headers);
            $writer->insertAll($records);

            $csv = Reader::createFromString($writer->toString());
            $csv->setHeaderOffset(0);
        } else {
            $csv->setHeaderOffset(0);
        }

        $stmt = new Statement();

        if ($offset) {
            $stmt = $stmt->offset((int) $offset);
        }

        if ($limit) {
            $stmt = $stmt->limit((int) $limit);
        }

        $records = $stmt->process($csv);

        return new TraversableImportDataSet($records);
    }

    public function exportData(array $configuration, ExportDefinitionInterface $definition, array $params): void
    {
        if (!array_key_exists('file', $params)) {
            return;
        }

        $file = $this->getFile($params);

        $headers = count($this->exportData) > 0 ? array_keys($this->exportData[0]) : [];

        $writer = Writer::createFromPath($file, 'w+');
        $writer->setDelimiter($configuration['delimiter']);
        $writer->setEnclosure($configuration['enclosure']);
        if (isset($configuration['escape'])) {
            $writer->setEscape($configuration['escape']);
        }
        $writer->insertOne($headers);
        $writer->insertAll($this->exportData);
    }

    public function addExportData(
        array $data,
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params,
    ): void {
        $this->exportData[] = $data;
    }
}
