<?php

namespace AdvancedImportExport\Model\Mapping;

/**
 * Class FromColumn
 * @package AdvancedImportExport\Model\Mapping
 */
class ToColumn extends AbstractColumn {

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $fieldtype;

    /**
     * @var array
     */
    public $config;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFieldtype()
    {
        return $this->fieldtype;
    }

    /**
     * @param string $fieldtype
     */
    public function setFieldtype($fieldtype)
    {
        $this->fieldtype = $fieldtype;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
}