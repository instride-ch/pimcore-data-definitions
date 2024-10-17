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

use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use Instride\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use Instride\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactProviderTrait;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\WriterInterface;
use OpenSpout\Writer\XLSX\Writer;
use Pimcore\Model\Asset;
use Pimcore\Tool\Storage;

class ExcelProvider extends AbstractFileProvider implements ImportProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    private string $exportPath;

    private WriterInterface $writer;

    public function testData(array $configuration): bool
    {
        return true;
    }

    public function getColumns(array $configuration): array
    {
        if ($configuration['exampleFile']) {
            $exampleFile = Asset::getById($configuration['exampleFile']);
            if (null !== $exampleFile) {
                $storage = Storage::get('asset');
                $stream = $storage->readStream($exampleFile->getFullPath());
                $reader = $this->createReader($stream);

                $sheetIterator = $reader->getSheetIterator();
                $sheetIterator->rewind();
                $rowIterator = $sheetIterator->current()->getRowIterator();
                if (null !== $rowIterator) {
                    $rowIterator->rewind();

                    return $this->buildColumns($rowIterator->current());
                }
            }
        } elseif ($configuration['excelHeaders']) {
            return $this->buildColumns(str_getcsv((string) $configuration['excelHeaders']));
        }

        return [];
    }

    public function getData(
        array $configuration,
        ImportDefinitionInterface $definition,
        array $params,
        FilterInterface $filter = null,
    ): ImportDataSetInterface {
        $file = $this->getFile($params);

        $reader = $this->createReader($file);
        $sheetIterator = $reader->getSheetIterator();
        $sheetIterator->rewind();
        $rowIterator = $sheetIterator->current()->getRowIterator();

        $headers = null;
        $headersCount = null;

        return new ImportDataSet($rowIterator, function (Row $row) use (&$headers, &$headersCount) {
            $rowArray = $row->toArray();

            if (null === $headers) {
                $headers = $rowArray;
                $headersCount = count($headers);

                return null;
            }

            $rowCount = count($rowArray);
            if ($rowCount < $headersCount) {
                // append missing values
                $rowArray = array_pad($rowArray, (int) $headersCount, null);
            } elseif ($rowCount >= $headersCount) {
                // remove overflow
                $rowArray = array_slice($rowArray, 0, $headersCount);
            }

            return array_combine($headers, $rowArray);
        });
    }

    public function addExportData(
        array $data,
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params,
    ): void {
        $headers = null;
        if (!isset($this->writer)) {
            $headers = array_keys($data);
        }
        $writer = $this->getWriter();
        $this->addHeaders($headers, $writer);

        foreach ($data as $key => $item) {
            if (is_object($item)) {
                $data[$key] = (string) $item;
            }
        }
        $writer->addRow(Row::fromValues(array_values($data)));
    }

    public function exportData(array $configuration, ExportDefinitionInterface $definition, array $params): void
    {
        $writer = $this->getWriter();
        $writer->close();

        if (!array_key_exists('file', $params)) {
            return;
        }

        $file = $this->getFile($params);
        copy($this->getExportPath(), $file);
        unlink($this->getExportPath());
    }

    public function provideArtifactStream($configuration, ExportDefinitionInterface $definition, $params)
    {
        return fopen($this->getExportPath(), 'rb');
    }

    private function createReader($path): ReaderInterface
    {
        $reader = $this->getXlsxReader();
        $reader->open($path);

        return $reader;
    }

    private function getWriter(): WriterInterface
    {
        if (!isset($this->writer)) {
            $this->writer = $this->getXlsxWriter();
            $this->writer->openToFile($this->getExportPath());
        }

        return $this->writer;
    }

    private function getExportPath(): string
    {
        if (!isset($this->exportPath)) {
            $this->exportPath = tempnam(sys_get_temp_dir(), 'excel_export_provider');
        }

        return $this->exportPath;
    }

    private function buildColumns($row): array
    {
        $headers = [];
        if (null !== $row) {
            foreach ($row as $header) {
                if (!$header) {
                    continue;
                }

                $fromColumn = new FromColumn();
                $fromColumn->setIdentifier($header);
                $fromColumn->setLabel($header);

                $headers[] = $fromColumn;
            }
        }

        return $headers;
    }

    private function addHeaders(?array $headers, WriterInterface $writer): void
    {
        if (null !== $headers) {
            $writer->addRow(Row::fromValues($headers));
        }
    }

    protected function getXlsxReader(): Reader
    {
        return new Reader();
    }

    protected function getXlsxWriter(): Writer
    {
        return new Writer();
    }
}
