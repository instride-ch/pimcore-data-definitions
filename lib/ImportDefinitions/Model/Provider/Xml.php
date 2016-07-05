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
use ImportDefinitions\Model\Mapping\FromColumn;
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
     * @var string
     */
    public $xmlExample;

    /**
     * @var
     */
    public $rootNode;

    /**
     * @return string
     */
    public function getXmlExample()
    {
        return $this->xmlExample;
    }

    /**
     * @param string $xmlExample
     */
    public function setXmlExample($xmlExample)
    {
        $this->xmlExample = $xmlExample;
    }

    /**
     * @return mixed
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * @param mixed $rootNode
     */
    public function setRootNode($rootNode)
    {
        $this->rootNode = $rootNode;
    }

    /**
     * @param array $arr
     * @return int
     */
    protected function getXmlDepth(array $arr)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        $depth = 0;
        foreach ($it as $v) {
            $it->getDepth() > $depth and $depth = $it->getDepth();
        }
        return $depth;
    }

    /**
     * @param $xml
     * @return mixed
     */
    protected function convertXmlToArray($xml)
    {
        $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);

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
        $data = $this->convertXmlToArray($this->getXmlExample());
        ;

        return $this->getXmlDepth($data) === 1;
    }

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public function getColumns()
    {
        $rows = $this->convertXmlToArray($this->getXmlExample());
        $returnHeaders = [];

        if ($this->getRootNode()) {
            $rows = $rows[$this->getRootNode()];
        }

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

    /**
     * @param Definition $definition
     * @param $params
     * @return Concrete[]
     */
    protected function runImport($definition, $params)
    {
        $file = PIMCORE_DOCUMENT_ROOT . "/" . $params['file'];
        $xml = file_get_contents($file);

        $objects = [];

        $data = $this->convertXmlToArray($xml);

        if ($this->getRootNode()) {
            $data = $data[$this->getRootNode()];
        }

        foreach ($data as $row) {
            $objects[] = $this->importRow($definition, $row);
        }

        return $objects;
    }

    /**
     * @param Definition $definition
     * @param $data
     *
     * @return Concrete
     */
    private function importRow($definition, $data)
    {
        $object = $this->getObjectForPrimaryKey($definition, $data);

        foreach ($definition->getMapping() as $map) {
            $value = $data[$map->getFromColumn()];

            $this->setObjectValue($object, $map, $value, $data);
        }

        $object->save();

        return $object;
    }
}
