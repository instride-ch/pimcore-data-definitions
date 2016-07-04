<?php

namespace ImportDefinitions\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Placeholder;

/**
 * Class Definition
 * @package ImportDefinitions
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
    public $objectPath;

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
     * @var Mapping[]
     */
    public $mapping;

    /**
     * @var string
     */
    public $cleaner;

    /**
     * @var string
     */
    public $key;

    /**
     * Get By Id.
     *
     * @param int $id
     *
     * @return Definition
     */
    public static function getById($id)
    {
        $cacheKey = 'importdefinition_'.$id;

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
     * @param $data
     * @return string
     */
    public function createPath($data) {
        $placeholderHelper = new Placeholder();
        return $placeholderHelper->replacePlaceholders($this->getObjectPath(), $data);
    }

    /**
     * @param $data
     * @return string
     */
    public function createKey($data) {
        $placeholderHelper = new Placeholder();
        return $placeholderHelper->replacePlaceholders($this->getKey(), $data);
    }

    /**
     * @param array $params
     */
    public function doImport($params = []) {
        $this->getProviderConfiguration()->doImport($this, $params);
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
     * @return Mapping[]
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param Mapping[] $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
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

    /**
     * @return string
     */
    public function getObjectPath()
    {
        return $this->objectPath;
    }

    /**
     * @param string $objectPath
     */
    public function setObjectPath($objectPath)
    {
        $this->objectPath = $objectPath;
    }

    /**
     * @return string
     */
    public function getCleaner()
    {
        return $this->cleaner;
    }

    /**
     * @param string $cleaner
     */
    public function setCleaner($cleaner)
    {
        $this->cleaner = $cleaner;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}