<?php

namespace AdvancedImportExport\Model\Provider;

use AdvancedImportExport\Model\AbstractProvider;
use AdvancedImportExport\Model\Mapping\FromColumn;

/**
 * CSV Import/Export Provider
 *
 * Class CSV
 * @package AdvancedImportExport\Provider
 */
class Csv extends AbstractProvider {

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
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public function getColumns()
    {
        $returnHeaders = [];
        $rows = str_getcsv($this->getCsvExample(), "\n"); //parse the rows

        if(count($rows) > 0) {
            $headerRow = $rows[0];

            $headers = str_getcsv($headerRow, $this->getDelimiter(), $this->getEnclosure());

            if(count($headers) > 0) {
                //First line are the headers
                foreach($headers as $header) {
                    $headerObj = new FromColumn();
                    $headerObj->setIdentifier($header);
                    $headerObj->setLabel($header);

                    $returnHeaders[] = $headerObj;
                }
            }

        }

        return $returnHeaders;
    }
}