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

use ImportDefinitionsBundle\Model\ExportDefinitionInterface;
use ImportDefinitionsBundle\Model\ImportMapping\FromColumn;
use ImportDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use ImportDefinitionsBundle\ProcessManager\ArtifactProviderTrait;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class CsvProvider extends AbstractFileProvider implements ProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    /**
     * @var array
     */
    private $exportData = [];

    /**
     * {@inheritdoc}
     */
    public function testData($configuration)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($configuration)
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
    public function getData($configuration, $definition, $params, $filter = null)
    {
        $csvHeaders = $configuration['csvHeaders'];
        $delimiter = $configuration['delimiter'];
        $enclosure = $configuration['enclosure'];

        $offset = $params['offset'];
        $limit = $params['limit'];

        $file = $this->getFile($params['file']);

        $csv = Reader::createFromPath($file, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setEnclosure($enclosure);

        if ($csvHeaders) {
            $headers = array_map(function(FromColumn $column) {
                return $column->getIdentifier();
            }, $this->getColumns($configuration));

            $writer = Writer::createFromString('');

            $stmt = new Statement();
            $records = $stmt->process($csv);

            $writer->insertOne($headers);
            $writer->insertAll($records);

            $csv = Reader::createFromString($writer->getContent());
            $csv->setHeaderOffset(0);
        }
        else {
            $csv->setHeaderOffset(0);
        }

        $stmt = new Statement();

        if ($offset) {
            $stmt = $stmt->offset(intval($offset));
        }

        if ($limit) {
            $stmt = $stmt->limit(intval($limit));
        }

        $records = $stmt->process($csv);

        return iterator_to_array($records);
    }

    /**
     * {@inheritdoc}
     */
    public function exportData($configuration, ExportDefinitionInterface $definition, $params)
    {
        if (!array_key_exists('file', $params)) {
            return;
        }

        $file = $this->getFile($params['file']);

        $headers = count($this->exportData) > 0 ? array_keys($this->exportData[0]) : [];

        $writer = Writer::createFromPath($file, 'w+');
        $writer->setDelimiter($configuration['delimiter']);
        $writer->setEnclosure($configuration['enclosure']);
        $writer->insertOne($headers);
        $writer->insertAll($this->exportData);
    }

    /**
     * {@inheritdoc}
     */
    public function addExportData(array $data, $configuration, ExportDefinitionInterface $definition, $params)
    {
        $this->exportData[] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function provideArtifactStream($configuration, ExportDefinitionInterface $definition, $params)
    {
        $headers = count($this->exportData) > 0 ? array_keys($this->exportData[0]) : [];

        $stream = fopen('php://memory','rw+');

        $writer = Writer::createFromStream($stream);
        $writer->setDelimiter($configuration['delimiter']);
        $writer->setEnclosure($configuration['enclosure']);
        $writer->insertOne($headers);
        $writer->insertAll($this->exportData);

        return $stream;
    }
}
