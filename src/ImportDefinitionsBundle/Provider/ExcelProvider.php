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

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\WriterInterface;
use Box\Spout\Writer\XLSX\Writer;
use ImportDefinitionsBundle\Model\ExportDefinitionInterface;
use ImportDefinitionsBundle\Model\ImportMapping\FromColumn;
use ImportDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use ImportDefinitionsBundle\ProcessManager\ArtifactProviderTrait;
use Pimcore\Model\Asset;

class ExcelProvider extends AbstractFileProvider implements ProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    /** @var string */
    private $exportPath;

    /** @var WriterInterface */
    private $writer;

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
            return $this->buildColumns(str_getcsv($configuration['excelHeaders']));
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getData($configuration, $definition, $params, $filter = null)
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

    /**
     * {@inheritdoc}
     */
    public function addExportData(array $data, $configuration, ExportDefinitionInterface $definition, $params)
    {
        $headers = null;
        if (null === $this->writer) {
            $headers = array_keys($data);
        }
        $writer = $this->getWriter();
        $this->addHeaders($headers, $writer);

        foreach ($data as $key => $item) {
            if (is_object($item)) {
                $data[$key] = (string) $item;
            }
        }
        $writer->addRow(array_values($data));
    }

    /**
     * {@inheritdoc}
     */
    public function exportData($configuration, ExportDefinitionInterface $definition, $params)
    {
        $writer = $this->getWriter();
        $writer->close();

        if (!array_key_exists('file', $params)) {
            return;
        }

        $file = $this->getFile($params['file']);
        rename($this->getExportPath(), $file);
    }

    /**
     * {@inheritdoc}
     */
    public function provideArtifactStream($configuration, ExportDefinitionInterface $definition, $params)
    {
        return fopen($this->getExportPath(), 'rb');
    }

    private function createReader(string $path): ReaderInterface
    {
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($path);

        return $reader;
    }

    private function getWriter(): WriterInterface
    {
        if (null === $this->writer) {
            $this->writer = WriterFactory::create(Type::XLSX);
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
     * @param array|null      $headers
     * @param WriterInterface $writer
     */
    private function addHeaders(?array $headers, WriterInterface $writer): void
    {
        if (null !== $headers) {
            $writer->addRow($headers);
        }
    }
}
