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

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Box\Spout\Writer\WriterInterface;
use Pimcore\Model\Asset;
use Wvision\Bundle\DataDefinitionsBundle\Exception\SpoutException;
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

    public function getColumns(array $configuration)
    {
        if ($configuration['exampleFile']) {
            $exampleFile = Asset::getById($configuration['exampleFile']);
            if (null !== $exampleFile) {
                $reader = $this->createReader(PIMCORE_ASSET_DIRECTORY . $exampleFile->getFullPath());

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

    public function getData(array $configuration, ImportDefinitionInterface $definition, array $params, $filter = null)
    {
        $file = $this->getFile($params['file']);

        $reader = $this->createReader($file);
        $sheetIterator = $reader->getSheetIterator();
        $sheetIterator->rewind();
        $rowIterator = $sheetIterator->current()->getRowIterator();

        $headers = null;
        $headersCount = null;

        return new ImportDataSet($rowIterator, function (array $row) use (&$headers, &$headersCount) {
            if (null === $headers) {
                $headers = $row;
                $headersCount = count($headers);

                return null;
            }

            $rowCount = count($row);
            if ($rowCount < $headersCount) {
                // append missing values
                $row = array_pad($row, $headersCount, null);
            } elseif ($rowCount >= $headersCount) {
                // remove overflow
                $row = array_slice($row, 0, $headersCount);
            }

            return array_combine($headers, $row);
        });
    }

    public function addExportData(array $data, array $configuration, ExportDefinitionInterface $definition, array $params): void
    {
        $headers = null;
        if (null === $this->writer) {
            $headers = array_keys($data);
        }
        $writer = $this->getWriter();
        $this->addHeaders($headers, $writer);

        foreach ($data as $key => $item) {
            if (is_object($item)) {
                $data[$key] = (string)$item;
            }
        }
        $writer->addRow($this->useSpoutLegacy ? array_values($data) : \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray(array_values($data)));
    }

    public function exportData(array $configuration, ExportDefinitionInterface $definition, array $params): void
    {
        $writer = $this->getWriter();
        $writer->close();

        if (!array_key_exists('file', $params)) {
            return;
        }

        $file = $this->getFile($params['file']);
        rename($this->getExportPath(), $file);
    }

    public function provideArtifactStream($configuration, ExportDefinitionInterface $definition, $params)
    {
        return fopen($this->getExportPath(), 'rb');
    }

    private function createReader(string $path): ReaderInterface
    {
        $reader = $this->getXlsxReader();
        $reader->open($path);

        return $reader;
    }

    private function getWriter(): WriterInterface
    {
        if (null === $this->writer) {
            $this->writer = $this->getXlsxWriter();
            $this->writer->openToFile($this->getExportPath());
        }

        return $this->writer;
    }

    private function getExportPath(): string
    {
        if (null === $this->exportPath) {
            $this->exportPath = tempnam(sys_get_temp_dir(), 'excel_export_provider');
        }

        return $this->exportPath;
    }

    /**
     * @param $row
     *
     * @return array
     */
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

    /**
     * @param array|null $headers
     * @param WriterInterface $writer
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    private function addHeaders(?array $headers, WriterInterface $writer): void
    {
        if (null !== $headers) {
            $writer->addRow($this->useSpoutLegacy ? $headers : \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($headers));
        }
    }

    /**
     * @return \Box\Spout\Reader\XLSX\Reader
     * @throws SpoutException
     */
    protected function getXlsxReader(): \Box\Spout\Reader\XLSX\Reader
    {
        if (class_exists(\Box\Spout\Reader\Common\Creator\ReaderEntityFactory::class)) {
            return \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createXLSXReader();
        }
        if (class_exists(\Box\Spout\Reader\ReaderFactory::class)) {
            $this->useSpoutLegacy = true;
            return \Box\Spout\Reader\ReaderFactory::create(\Box\Spout\Common\Type::XLSX);
        }

        throw new SpoutException('Error creating Spout XLSX Reader');
    }

    /**
     * @return \Box\Spout\Writer\XLSX\Writer
     * @throws SpoutException
     */
    protected function getXlsxWriter(): \Box\Spout\Writer\XLSX\Writer
    {
        if (class_exists(\Box\Spout\Writer\Common\Creator\WriterEntityFactory::class)) {
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
        }
        if (class_exists(\Box\Spout\Writer\WriterFactory::class)) {
            $this->useSpoutLegacy = true;
            return \Box\Spout\Reader\WriterFactory::create(\Box\Spout\Common\Type::XLSX);
        }

        throw new SpoutException('Error creating Spout XLSX Writer');
    }
}
