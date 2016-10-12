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
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions\Model\Provider;

use ImportDefinitions\Model\AbstractProvider;
use ImportDefinitions\Model\Definition;
use ImportDefinitions\Model\Filter\AbstractFilter;
use ImportDefinitions\Model\Mapping\FromColumn;
use Pimcore\Model\Asset;
use Pimcore\Model\Object\Concrete;

/**
 * XML Import Provider
 *
 * Class Xml
 * @package ImportDefinitions\Provider
 */
class Xml extends AbstractProvider
{

    /**
     * @var int
     */
    public $exampleFile;

    /**
     * @var string
     */
    public $xPath;

    /**
     * @var string
     */
    public $exampleXPath;

    /**
     * @return int
     */
    public function getExampleFile()
    {
        return $this->exampleFile;
    }

    /**
     * @param int $exampleFile
     */
    public function setExampleFile($exampleFile)
    {
        $this->exampleFile = $exampleFile;
    }

    /**
     * @return string
     */
    public function getXPath()
    {
        return $this->xPath;
    }

    /**
     * @param string $xPath
     */
    public function setXPath($xPath)
    {
        $this->xPath = $xPath;
    }

    /**
     * @return string
     */
    public function getExampleXPath()
    {
        return $this->exampleXPath;
    }

    /**
     * @param string $exampleXPath
     */
    public function setExampleXPath($exampleXPath)
    {
        $this->exampleXPath = $exampleXPath;
    }

    /**
     * @param $xml
     * @return mixed
     */
    protected function convertXmlToArray($xml, $xpath)
    {
        $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $xml = $xml->xpath($xpath);
        // $xml->xpath

        $json = json_encode($xml);
        $array = json_decode($json, true);

        foreach($array as &$arrayEntry) {
            if(array_key_exists("@attributes", $arrayEntry)) {
                foreach($arrayEntry['@attributes'] as $key => $value) {
                    $arrayEntry['attr_' . $key] = $value;
                }
            }
        }

        return $array;
    }

    /**
     * test data
     *
     * @return boolean
     * @throws \Exception
     */
    public function testData()
    {
        return true;
    }

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public function getColumns()
    {
        $exampleFile = Asset::getById($this->getExampleFile());
        $rows = $this->convertXmlToArray($exampleFile->getData(), $this->getExampleXPath());
        $rows = $rows[0];

        $returnHeaders = [];

        if (count($rows) > 0)
        {
            $firstRow = $rows;

            foreach ($firstRow as $key => $val)
            {
                $headerObj = new FromColumn();
                $headerObj->setIdentifier($key);
                $headerObj->setLabel($key);

                $returnHeaders[] = $headerObj;
            }
        }

        return $returnHeaders;
    }

    /**
     * @param $definition
     * @param $params
     * @param null $filter
     * @return array
     */
    protected function getData($definition, $params, $filter = null)
    {
        $file = PIMCORE_DOCUMENT_ROOT . "/" . $params['file'];
        $xml = file_get_contents($file);

        $data = $this->convertXmlToArray($xml, $this->getXPath());

        return $data;
    }
}
