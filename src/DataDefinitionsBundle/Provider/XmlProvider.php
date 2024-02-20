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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Tool\Storage;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use Instride\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use Instride\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactProviderTrait;
use XMLWriter;
use function count;

class XmlProvider extends AbstractFileProvider implements ImportProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    private XMLWriter $writer;
    private string $exportPath;
    private int $exportCounter = 0;

    protected function convertXmlToArray($xml, $xpath)
    {
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xml = $xml->xpath($xpath);

        $json = json_encode($xml);
        $array = json_decode($json, true);

        foreach ($array as &$arrayEntry) {
            if (array_key_exists('@attributes', $arrayEntry)) {
                foreach ($arrayEntry['@attributes'] as $key => $value) {
                    $arrayEntry['attr_'.$key] = $value;
                }
            }
        }

        return $array;
    }

    public function testData(array $configuration): bool
    {
        return true;
    }

    public function getColumns(array $configuration): array
    {
        $exampleFile = Asset::getById($configuration['exampleFile']);
        $rows = $this->convertXmlToArray($exampleFile->getData(), $configuration['exampleXPath']);
        $rows = $rows[0];

        $returnHeaders = [];

        if (count($rows) > 0) {
            $firstRow = $rows;

            foreach ($firstRow as $key => $val) {
                $headerObj = new FromColumn();
                $headerObj->setIdentifier($key);
                $headerObj->setLabel($key);

                $returnHeaders[] = $headerObj;
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
        $file = $this->getFile($params);
        $xml = file_get_contents($file);

        return new ArrayImportDataSet($this->convertXmlToArray($xml, $configuration['xPath']));
    }

    public function addExportData(
        array $data,
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params
    ): void {
        $writer = $this->getXMLWriter();

        $writer->startElement('object');
        $this->serializeCollection($writer, $data);
        $writer->endElement();

        $this->exportCounter++;
        if ($this->exportCounter >= 50) {
            $this->flush($writer);
            $this->exportCounter = 0;
        }
    }

    public function exportData(array $configuration, ExportDefinitionInterface $definition, array $params): void
    {
        $writer = $this->getXMLWriter();

        // </root>
        $writer->endElement();
        $this->flush($writer);

        // XSLT transformation support
        if (array_key_exists('xsltPath', $configuration) && $configuration['xsltPath']) {
            $dataPath = $this->getExportPath();

            $storage = Storage::get('asset');
            $path = ltrim($configuration['xsltPath'], '/');

            if (!$storage->fileExists($path)) {
                throw new RuntimeException(sprintf('Passed XSLT file "%1$s" not found', $path));
            }

            $extension = pathinfo($configuration['xsltPath'], PATHINFO_EXTENSION);
            $workingPath = File::getLocalTempFilePath($extension);
            file_put_contents($workingPath, $storage->read($path));

            $this->exportPath = tempnam(sys_get_temp_dir(), 'xml_export_xslt_transformation');
            $cmd = [
                'xsltproc',
                '-v',
                '--noout',
                '--output',
                $this->getExportPath(),
                $workingPath,
                $dataPath,
            ];
            $process = new Process($cmd);
            $process->setTimeout(null);
            $process->run();

            if (!stream_is_local($path)) {
                unlink($workingPath);
            }

            if (false === $process->isSuccessful()) {
                throw new RuntimeException($process->getErrorOutput());
            }
        }

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

    private function getXMLWriter(): XMLWriter
    {
        if (!isset($this->writer)) {
            $this->writer = new XMLWriter();
            $this->writer->openMemory();
            $this->writer->setIndent(true);
            $this->writer->startDocument('1.0', 'UTF-8');

            // <root>
            $this->writer->startElement('export');
        }

        return $this->writer;
    }

    private function getExportPath(): string
    {
        if (!isset($this->exportPath)) {
            $this->exportPath = tempnam(sys_get_temp_dir(), 'xml_export_provider');
        }

        return $this->exportPath;
    }

    private function flush(XMLWriter $writer): void
    {
        file_put_contents($this->getExportPath(), $writer->flush(true), FILE_APPEND);
    }

    private function serialize(XMLWriter $writer, ?string $name, $data, ?int $key = null): void
    {
        if (is_scalar($data)) {
            $writer->startElement('property');
            if (null !== $name) {
                $writer->writeAttribute('name', $name);
            }
            if (null !== $key) {
                $writer->writeAttribute('key', (string)$key);
            }
            if (is_string($data)) {
                $writer->writeCdata($data);
            } else {
                // TODO: should be more elaborate/exact for "non-string" scalar values
                $writer->text((string)$data);
            }
            $writer->endElement();
        } else {
            if (is_array($data)) {
                $writer->startElement('collection');
                if (null !== $name) {
                    $writer->writeAttribute('name', $name);
                }
                if (null !== $key) {
                    $writer->writeAttribute('key', (string)$key);
                }
                $this->serializeCollection($writer, $data);
                $writer->endElement();
            } else {
                if ((string)$data) {
                    $writer->startElement('property');
                    if (null !== $name) {
                        $writer->writeAttribute('name', $name);
                    }
                    if (null !== $key) {
                        $writer->writeAttribute('key', (string)$key);
                    }
                    $writer->writeCdata((string)$data);
                    $writer->endElement();
                }
            }
        }
    }

    private function serializeCollection(XMLWriter $writer, array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $this->serialize($writer, null, $value, $key);
            } else {
                $this->serialize($writer, $key, $value);
            }
        }
    }
}
