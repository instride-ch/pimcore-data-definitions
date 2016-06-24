<?php

namespace AdvancedImportExport\Model;

use Pimcore\Model\AbstractModel;

/**
 * Class Definition
 * @package AdvancedImportExport
 */
class Definition extends AbstractModel {
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $provider;

    /**
     * @var string
     */
    public $class;

    /**
     * @var AbstractProvider
     */
    public $providerConfiguration;

    /**
     * @var int
     */
    public $creationDate;

    /**
     * @var int
     */
    public $modificationDate;

    /**
     * Get By Id.
     *
     * @param int $id
     *
     * @return Definition
     */
    public static function getById($id)
    {
        $cacheKey = 'advancedimportexport_definition_'.$id;

        try {
            $definitionEntry = \Zend_Registry::get($cacheKey);
            if (!$definitionEntry) {
                throw new \Exception('Definition in registry is null');
            }
        } catch (\Exception $e) {
            try {
                $definitionEntry = new self();
                \Zend_Registry::set($cacheKey, $definitionEntry);
                $definitionEntry->setId(intval($id));
                $definitionEntry->getDao()->getById();
            } catch (\Exception $e) {
                \Logger::error($e);

                return null;
            }
        }

        return $definitionEntry;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return AbstractProvider
     */
    public function getProviderConfiguration()
    {
        return $this->providerConfiguration;
    }

    /**
     * @param AbstractProvider $providerConfiguration
     */
    public function setProviderConfiguration($providerConfiguration)
    {
        $this->providerConfiguration = $providerConfiguration;
    }

    /**
     * @return int
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param int $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param string $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return int
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param int $modificationDate
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }
}