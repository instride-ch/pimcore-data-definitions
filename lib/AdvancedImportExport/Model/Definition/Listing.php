<?php

namespace AdvancedImportExport\Model\Definition;

use AdvancedImportExport\Model\Definition;
use Pimcore\Model;

/**
 * Class Listing
 * @package AdvancedImportExport\Model\Definition
 */
class Listing extends Model\Listing\JsonListing
{
    /**
     * Contains the results of the list. They are all an instance of Configuration.
     *
     * @var array
     */
    public $definitions = null;

    /**
     * Get Configurations.
     *
     * @return Definition[]
     */
    public function getDefinitions()
    {
        if (is_null($this->definitions)) {
            $this->load();
        }

        return $this->definitions;
    }

    /**
     * Set Definitions.
     *
     * @param array $definitions
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;
    }
}
