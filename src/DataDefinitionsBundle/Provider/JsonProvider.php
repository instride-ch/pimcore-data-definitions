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

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

use function count;
use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use Instride\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactGenerationProviderInterface;
use Instride\Bundle\DataDefinitionsBundle\ProcessManager\ArtifactProviderTrait;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class JsonProvider extends AbstractFileProvider implements ImportProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    /**
     * @var array
     */
    private $exportData = [];

    /**
     * Calculate depth
     *
     * @return int
     */
    protected function getJsonDepth(array $arr)
    {
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
        $depth = 0;
        foreach ($it as $v) {
            $it->getDepth() > $depth and $depth = $it->getDepth();
        }

        return $depth;
    }

    public function testData(array $configuration): bool
    {
        $jsonExample = $configuration['jsonExample'];

        return $this->getJsonDepth(json_decode($jsonExample, true)) === 1;
    }

    public function getColumns(array $configuration): array
    {
        $jsonExample = $configuration['jsonExample'];

        $rows = json_decode($jsonExample, true);
        $returnHeaders = [];

        if (count($rows) > 0) {
            $firstRow = $rows[0];

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
        FilterInterface $filter = null,
    ): ImportDataSetInterface {
        $file = $this->getFile($params);

        if (file_exists($file)) {
            $json = file_get_contents($file);

            return new ArrayImportDataSet(\json_decode($json, true));
        }

        return new ArrayImportDataSet([]);
    }

    public function exportData(array $configuration, ExportDefinitionInterface $definition, array $params): void
    {
        if (!array_key_exists('file', $params)) {
            return;
        }

        $file = $this->getFile($params);

        file_put_contents($file, json_encode($this->exportData));
    }

    public function addExportData(
        array $data,
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params,
    ): void {
        $this->exportData[] = $data;
    }

    public function provideArtifactStream($configuration, ExportDefinitionInterface $definition, array $params)
    {
        $stream = fopen('php://memory', 'rw+');
        fwrite($stream, json_encode($this->exportData));

        return $stream;
    }
}
