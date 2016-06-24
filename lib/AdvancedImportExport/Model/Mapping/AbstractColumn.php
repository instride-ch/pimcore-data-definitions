<?php

namespace AdvancedImportExport\Model\Mapping;

/**
 * Class AbstractColumn
 * @package AdvancedImportExport\Model\Mapping
 */
abstract class AbstractColumn {
    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            $setter = 'set'.ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}