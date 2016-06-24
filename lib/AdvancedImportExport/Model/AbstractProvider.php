<?php

namespace AdvancedImportExport\Model;
use AdvancedImportExport\Model\Mapping\FromColumn;

/**
 * Base Class every Provider needs to implement
 *
 * Class AbstractProvider
 * @package AdvancedImportExport
 */
abstract class AbstractProvider {
    /**
     * available Providers.
     *
     * @var array
     */
    public static $availableProviders = array('csv', 'sql');

    /**
     * @var Mapping[]
     */
    public $mappings;

    /**
     * Add Provider.
     *
     * @param $provider
     */
    public static function addProvider($provider)
    {
        if (!in_array($provider, self::$availableProviders)) {
            self::$availableProviders[] = $provider;
        }
    }

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

            if($key === "mappings") {
                $mappings = [];

                if(is_array($value)) {
                    foreach($value as $vMap) {
                        $mapping = new Mapping();
                        $mapping->setValues($vMap);

                        $mappings[] = $mapping;
                    }

                    $value = $mappings;
                }
            }

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return array
     */
    public static function getAvailableProviders()
    {
        return self::$availableProviders;
    }

    /**
     * @param array $availableProviders
     */
    public static function setAvailableProviders($availableProviders)
    {
        self::$availableProviders = $availableProviders;
    }

    /**
     * @return Mapping[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param Mapping[] $mappings
     */
    public function setMappings($mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public abstract function getColumns();
}