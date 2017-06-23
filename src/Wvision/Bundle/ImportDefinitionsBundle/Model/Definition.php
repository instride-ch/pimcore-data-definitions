<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\ImportDefinitionsBundle\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\AbstractModel;
use Pimcore\Placeholder;

class Definition extends AbstractModel implements DefinitionInterface, ResourceInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string<
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
     * @var array
     */
    public $configuration;

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
     * @var string
     */
    public $filter;

    /**
     * @var bool
     */
    public $renameExistingObjects;

    /**
     * @var bool
     */
    public $relocateExistingObjects;

    /**
     * @var bool
     */
    public $skipNewObjects = false;

    /**
     * @var bool
     */
    public $skipExistingObjects = false;

    /**
     * @var string
     */
    public $runner;

    /**
     * @var boolean
     */
    public $createVersion;

    /**
     * @var boolean
     */
    public $stopOnException;

    /**
     * @var int
     */
    public $failureNotificationDocument;

    /**
     * @var int
     */
    public $successNotificationDocument;

    /**
     * Get By Id.
     *
     * @param int $id
     *
     * @return Definition
     */
    public static function getById($id)
    {
        $definitionEntry = new self();
        $definitionEntry->setId(intval($id));
        $definitionEntry->getDao()->getById();

        return $definitionEntry;
    }

    /**
     * @param $data
     * @return string
     */
    public function createPath($data)
    {
        $placeholderHelper = new Placeholder();
        return $placeholderHelper->replacePlaceholders($this->getObjectPath(), $data);
    }

    /**
     * @param $data
     * @return string
     */
    public function createKey($data)
    {
        $placeholderHelper = new Placeholder();
        return $placeholderHelper->replacePlaceholders($this->getKey(), $data);
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
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
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

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return boolean
     */
    public function getRenameExistingObjects()
    {
        return $this->renameExistingObjects;
    }

    /**
     * @param boolean $renameExistingObjects
     */
    public function setRenameExistingObjects($renameExistingObjects)
    {
        $this->renameExistingObjects = $renameExistingObjects;
    }

    /**
     * @return boolean
     */
    public function getRelocateExistingObjects()
    {
        return $this->relocateExistingObjects;
    }

    /**
     * @param boolean $relocateExistingObjects
     */
    public function setRelocateExistingObjects($relocateExistingObjects)
    {
        $this->relocateExistingObjects = $relocateExistingObjects;
    }

    /**
     * @return string
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @param string $runner
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;
    }

    /**
     * @return boolean
     */
    public function getCreateVersion()
    {
        return $this->createVersion;
    }

    /**
     * @param boolean $createVersion
     */
    public function setCreateVersion($createVersion)
    {
        $this->createVersion = $createVersion;
    }

    /**
     * @return boolean
     */
    public function getStopOnException()
    {
        return $this->stopOnException;
    }

    /**
     * @param boolean $stopOnException
     */
    public function setStopOnException($stopOnException)
    {
        $this->stopOnException = $stopOnException;
    }

    /**
     * @return int
     */
    public function getFailureNotificationDocument()
    {
        return $this->failureNotificationDocument;
    }

    /**
     * @param int $failureNotificationDocument
     */
    public function setFailureNotificationDocument($failureNotificationDocument)
    {
        $this->failureNotificationDocument = $failureNotificationDocument;
    }

    /**
     * @return int
     */
    public function getSuccessNotificationDocument()
    {
        return $this->successNotificationDocument;
    }

    /**
     * @param int $successNotificationDocument
     */
    public function setSuccessNotificationDocument($successNotificationDocument)
    {
        $this->successNotificationDocument = $successNotificationDocument;
    }

    /**
     * @return bool
     */
    public function getSkipNewObjects()
    {
        return $this->skipNewObjects;
    }

    /**
     * @param bool $skipNewObjects
     */
    public function setSkipNewObjects($skipNewObjects)
    {
        $this->skipNewObjects = $skipNewObjects;
    }

    /**
     * @return bool
     */
    public function getSkipExistingObjects()
    {
        return $this->skipExistingObjects;
    }

    /**
     * @param bool $skipExistingObjects
     */
    public function setSkipExistingObjects($skipExistingObjects)
    {
        $this->skipExistingObjects = $skipExistingObjects;
    }
}
