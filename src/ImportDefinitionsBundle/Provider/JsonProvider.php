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

class JsonProvider extends AbstractFileProvider implements ProviderInterface, ExportProviderInterface, ArtifactGenerationProviderInterface
{
    use ArtifactProviderTrait;

    /**
     * @var array
     */
    private $exportData = [];

    /**
     * Calculate depth
     *
     * @param array $arr
     * @return int
     */
    protected function getJsonDepth(array $arr)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        $depth = 0;
        foreach ($it as $v) {
            $it->getDepth() > $depth and $depth = $it->getDepth();
        }

        return $depth;
    }

    /**
     * {@inheritdoc}
     */
    public function testData($configuration)
    {
        $jsonExample = $configuration['jsonExample'];

        return $this->getJsonDepth(json_decode($jsonExample, true)) === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($configuration)
    {
        $jsonExample = $configuration['jsonExample'];

        $rows = json_decode($jsonExample, true);
        $returnHeaders = [];

        if (\count($rows) > 0) {
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

    /**
     * {@inheritdoc}
     */
    public function getData($configuration, $definition, $params, $filter = null)
    {
        $file = $this->getFile($params['file']);

        if (file_exists($file)) {
            $json = file_get_contents($file);

            return json_decode($json, true);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function exportData($configuration, ExportDefinitionInterface $definition, $params)
    {
        $file = $this->getFile($params['file']);

        file_put_contents($file, json_encode($this->exportData));
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
        $stream = fopen('php://memory', 'rw+');
        fwrite($stream, json_encode($this->exportData));

        return $stream;
    }
}
