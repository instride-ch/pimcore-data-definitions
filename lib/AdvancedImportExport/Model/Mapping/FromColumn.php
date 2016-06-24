<?php

namespace AdvancedImportExport\Model\Mapping;

/**
 * Class FromColumn
 * @package AdvancedImportExport\Model\Mapping
 */
class FromColumn extends AbstractColumn {
    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }
}