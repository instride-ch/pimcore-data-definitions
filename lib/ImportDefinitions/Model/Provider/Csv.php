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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
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
     * @var string
     */
    public $csvHeaders;

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
     * @return string
     */
    public function getCsvHeaders()
    {
        return $this->csvHeaders;
    }

    /**
     * @param string $csvHeaders
     */
    public function setCsvHeaders($csvHeaders)
    {
        $this->csvHeaders = $csvHeaders;
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
        $rows = str_getcsv($this->getCsvHeaders() ? $this->getCsvHeaders() : $this->getCsvExample(), "\n"); //parse the rows

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
     * @param $definition
     * @param $params
     * @param null $filter
     * @return array
     */
    protected function getData($definition, $params, $filter = null)
    {
        $file = PIMCORE_DOCUMENT_ROOT . "/" . $params['file'];

        $columnMapping = [];

        if ($this->getCsvHeaders()) {
            $columnMapping = $this->getColumns();
        }

        $data = [];

        $row = 0;
        if (($handle = fopen($file, "r")) !== false) {
            while (($csvData = fgetcsv($handle, 1000, $this->getDelimiter(), $this->getEnclosure() ? $this->getEnclosure() : chr(8))) !== false) {
                $num = count($csvData);

                //Make Column Mapping
                if ($row === 0 && !$this->getCsvHeaders()) {
                    for ($c = 0; $c < $num; $c++) {
                        $columnMapping[] = $csvData[$c];
                    }
                } else {
                    $mappedData = [];

                    foreach ($csvData as $index => $col) {
                        $mappedData[$columnMapping[$index]] = $col;
                    }

                    $data[] = $mappedData;
                }

                $row++;
            }
            fclose($handle);
        }

        return $data;
    }
}
