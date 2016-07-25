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
use Pimcore\Model\Object\Concrete;

/**
 * CSV Import Provider
 *
 * Class Csv
 * @package ImportDefinitions\Provider
 */
class Csv extends AbstractProvider
{

    /**
     * @var string
     */
    public $csvExample;

    /**
     * @var string
     */
    public $delimiter;

    /**
     * @var string
     */
    public $enclosure;

    /**
     * @return string
     */
    public function getCsvExample()
    {
        return $this->csvExample;
    }

    /**
     * @param string $csvExample
     */
    public function setCsvExample($csvExample)
    {
        $this->csvExample = $csvExample;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
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
        $returnHeaders = [];
        $rows = str_getcsv($this->getCsvExample(), "\n"); //parse the rows

        if (count($rows) > 0) {
            $headerRow = $rows[0];

            $headers = str_getcsv($headerRow, $this->getDelimiter(), $this->getEnclosure() ? $this->getEnclosure() : chr(8));

            if (count($headers) > 0) {
                //First line are the headers
                foreach ($headers as $header) {
                    $headerObj = new FromColumn();
                    $headerObj->setIdentifier($header);
                    $headerObj->setLabel($header);

                    $returnHeaders[] = $headerObj;
                }
            }
        }

        return $returnHeaders;
    }

    /**
     * @param Definition $definition
     * @param $params
     * @param AbstractFilter|null $filter
     *
     * @return Concrete[]
     */
    protected function runImport($definition, $params, $filter = null)
    {
        $file = PIMCORE_DOCUMENT_ROOT . "/" . $params['file'];

        $columnMapping = [];
        $objects = [];

        $row = 0;
        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, $this->getDelimiter(), $this->getEnclosure() ? $this->getEnclosure() : chr(8))) !== false) {
                $num = count($data);

                //Make Column Mapping
                if ($row === 0) {
                    for ($c = 0; $c < $num; $c++) {
                        $columnMapping[] = $data[$c];
                    }
                } else {
                    $object = $this->importCsvRow($definition, $columnMapping, $data, $filter);

                    if($object) {
                        $objects[] = $object;
                    }
                }

                $row++;
            }
            fclose($handle);
        }

        return $objects;
    }

    /**
     * @param Definition $definition
     * @param $columnMapping
     * @param $data
     * @param AbstractFilter $filter
     *
     * @return Concrete
     */
    protected function importCsvRow($definition, $columnMapping, $data, $filter = null)
    {
        //Convert Data to map
        $mappedData = [];

        foreach ($data as $index => $col) {
            $mappedData[$columnMapping[$index]] = $col;
        }

        return parent::importRow($definition, $mappedData, $filter);
    }
}
