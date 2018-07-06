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

use Pimcore\Model\Asset;
use ImportDefinitionsBundle\Model\Mapping\FromColumn;

class XmlProvider implements ProviderInterface
{
    /**
     * @param $xml
     * @param $xpath
     * @return mixed
     */
    protected function convertXmlToArray($xml, $xpath)
    {
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xml = $xml->xpath($xpath);

        $json = json_encode($xml);
        $array = json_decode($json, true);

        foreach ($array as &$arrayEntry) {
            if (array_key_exists('@attributes', $arrayEntry)) {
                foreach ($arrayEntry['@attributes'] as $key => $value) {
                    $arrayEntry['attr_' . $key] = $value;
                }
            }
        }

        return $array;
    }

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
        $exampleFile = Asset::getById($configuration['exampleFile']);
        $rows = $this->convertXmlToArray($exampleFile->getData(), $configuration['exampleXPath']);
        $rows = $rows[0];

        $returnHeaders = [];

        if (\count($rows) > 0) {
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

    /**
     * {@inheritdoc}
     */
    public function getData($configuration, $definition, $params, $filter = null)
    {
        $file = sprintf('%s/%s', PIMCORE_PROJECT_ROOT, $params['file']);
        $xml = file_get_contents($file);

        return $this->convertXmlToArray($xml, $configuration['xPath']);
    }
}
