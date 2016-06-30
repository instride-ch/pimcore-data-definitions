<?php

namespace AdvancedImportExport\Model\Provider;

use AdvancedImportExport\Model\AbstractProvider;
use AdvancedImportExport\Model\Definition;
use AdvancedImportExport\Model\Mapping\FromColumn;
use Pimcore\Model\Object\Concrete;

/**
 * Json Import Provider
 *
 * Class Json
 * @package AdvancedImportExport\Provider
 */
class Json extends AbstractProvider {

    /**
     * @var string
     */
    public $jsonExample;

    /**
     * @return string
     */
    public function getJsonExample()
    {
        return $this->jsonExample;
    }

    /**
     * @param string $jsonExample
     */
    public function setJsonExample($jsonExample)
    {
        $this->jsonExample = $jsonExample;
    }

    protected function getJsonDepth(array $arr) {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        $depth = 0;
        foreach ( $it as $v ) {
            $it->getDepth() > $depth and $depth = $it->getDepth();
        }
        return $depth;
    }

    /**
     * test data
     *
     * @return boolean
     * @throws \Exception
     */
    public function testData()
    {
        return $this->getJsonDepth(\Zend_Json::decode($this->getJsonExample())) === 1;
    }

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public function getColumns()
    {
        $rows = \Zend_Json::decode($this->getJsonExample());
        $returnHeaders = [];

        if(count($rows) > 0) {
            $firstRow = $rows[0];

            foreach($firstRow as $key => $val) {
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
        $json = file_get_contents($file);

        $objects = [];

        $data = \Zend_Json::decode($json);

        foreach($data as $row) {
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
    private function importRow($definition, $data) {
        $object = $this->getObjectForPrimaryKey($definition, $data);

        foreach($definition->getMapping() as $map) {
            $value = $data[$map->getFromColumn()];

            $this->setObjectValue($object, $map, $value, $data);
        }

        $object->save();

        return $object;
    }
}