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

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Writer\WriterInterface;
use Pimcore\Model\Asset;
use Pimcore\Tool\Storage;
use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use Wvision\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use Wvision\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactProviderTrait;

class ExcelProvider extends AbstractFileProvider implements ImportProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    private string $exportPath;

    private WriterInterface $writer;
    protected bool $useSpoutLegacy = false;

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
            return $this->buildColumns(str_getcsv((string)$configuration['excelHeaders']));
        }

        return [];
    }

    public function getData(
        array $configuration,
        ImportDefinitionInterface $definition,
        array $params,
        FilterInterface $filter = null
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
                $rowArray = array_pad($rowArray, (int)$headersCount, null);
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
        array $params
    ): void {
        $headers = null;
        if (!isset($this->writer)) {
            $headers = array_keys($data);
        }
        $writer = $this->getWriter();
        $this->addHeaders($headers, $writer);

        foreach ($data as $key => $item) {
            if (is_object($item)) {
                $data[$key] = (string)$item;
            }
        }
        $writer->addRow(
            $this->useSpoutLegacy ? array_values(
                $data
            ) : \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray(array_values($data))
        );
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
            $writer->addRow(
                $this->useSpoutLegacy ? $headers : \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray(
                    $headers
                )
            );
        }
    }

    protected function getXlsxReader(): \Box\Spout\Reader\XLSX\Reader
    {
        return \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createXLSXReader();
    }

    protected function getXlsxWriter(): \Box\Spout\Writer\XLSX\Writer
    {
        return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
    }
}
