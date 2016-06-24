<?php

namespace AdvancedImportExport\Model;
use AdvancedImportExport\Model\Mapping\FromColumn;
use AdvancedImportExport\Model\Mapping\ToColumn;

/**
 * Class Mapping
 * @package AdvancedImportExport\Model
 */
class Mapping {
    /**
     * @var FromColumn
     */
    public $fromColumn;

    /**
     * @var ToColumn
     */
    public $toColumn;

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            if($key === "fromColumn") {
                $fromCol = new FromColumn();
                $fromCol->setValues($value);

                $value = $fromCol;
            } else if($key === "toColumn") {
                $toCol = new ToColumn();
                $toCol->setValues($value);

                $value = $toCol;
            }

            $setter = 'set'.ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}