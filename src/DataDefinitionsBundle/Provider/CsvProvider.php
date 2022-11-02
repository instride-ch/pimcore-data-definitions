<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Provider;

use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use Wvision\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use Wvision\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactProviderTrait;
use function chr;
use function count;

class CsvProvider extends AbstractFileProvider implements ImportProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    private array $exportData = [];

    public function testData(array $configuration): bool
    {
        return true;
    }

    public function getColumns(array $configuration): array
    {
        $csvHeaders = (string)$configuration['csvHeaders'];
        $csvExample = $configuration['csvExample'];
        $delimiter = $configuration['delimiter'];
        $enclosure = $configuration['enclosure'];

        $returnHeaders = [];
        $rows = str_getcsv($csvHeaders ?: $csvExample, "\n"); //parse the rows

        if (count($rows) > 0) {
            $headerRow = $rows[0];

            $headers = str_getcsv($headerRow, $delimiter, $enclosure ?: chr(8));

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
        FilterInterface $filter = null
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
            $stmt = $stmt->offset((int)$offset);
        }

        if ($limit) {
            $stmt = $stmt->limit((int)$limit);
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
        $writer->setEscape('');
        $writer->insertOne($headers);
        $writer->insertAll($this->exportData);
    }

    public function addExportData(
        array $data,
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params
    ): void {
        $this->exportData[] = $data;
    }

    public function provideArtifactStream(array $configuration, ExportDefinitionInterface $definition, array $params)
    {
        $headers = count($this->exportData) > 0 ? array_keys($this->exportData[0]) : [];

        $stream = fopen('php://memory', 'rw+');

        $writer = Writer::createFromStream($stream);
        $writer->setDelimiter($configuration['delimiter']);
        $writer->setEnclosure($configuration['enclosure']);
        $writer->setEscape('');
        $writer->insertOne($headers);
        $writer->insertAll($this->exportData);

        return $stream;
    }
}
