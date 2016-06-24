<?php

namespace AdvancedImportExport\Model\Mapping;

/**
 * Class ToColumn
 * @package AdvancedImportExport\Model\Mapping
 */
class ToColumn extends AbstractColumn {
    /**
     * @var string
     */
    public $identifier;

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