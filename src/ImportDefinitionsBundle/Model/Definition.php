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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Model;

use Pimcore\Model\AbstractModel;

class Definition extends AbstractModel implements DefinitionInterface
{
    /**
     * @var null|int
     */
    public $id;

    /**
     * @var null|string
     */
    public $name;

    /**
     * @var null|string
     */
    public $provider;

    /**
     * @var null|string
     */
    public $objectPath;

    /**
     * @var null|int|string
     */
    public $class;

    /**
     * @var null|array
     */
    public $configuration;

    /**
     * @var null|int
     */
    public $creationDate;

    /**
     * @var null|int
     */
    public $modificationDate;

    /**
     * @var null|Mapping[]
     */
    public $mapping;

    /**
     * @var null|string
     */
    public $cleaner;

    /**
     * @var null|string
     */
    public $key;

    /**
     * @var null|string
     */
    public $filter;

    /**
     * @var null|bool
     */
    public $renameExistingObjects;

    /**
     * @var null|bool
     */
    public $relocateExistingObjects;

    /**
     * @var null|bool
     */
    public $skipNewObjects = false;

    /**
     * @var null|bool
     */
    public $skipExistingObjects = false;

    /**
     * @var null|string
     */
    public $runner;

    /**
     * @var null|boolean
     */
    public $createVersion;

    /**
     * @var null|boolean
     */
    public $stopOnException;

    /**
     * @var null|int
     */
    public $failureNotificationDocument;

    /**
     * @var null|int
     */
    public $successNotificationDocument;

    /**
     * Get By Id
     *
     * @param int $id
     * @return Definition
     */
    public static function getById($id)
    {
        $definitionEntry = new self();
        $definitionEntry->setId((int) $id);
        $definitionEntry->getDao()->getById();

        return $definitionEntry;
    }

    /**
     * @return int|mixed|null
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
     * @return mixed|null|string
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
     * @return array|mixed|null
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
     * @return int|mixed|null|string
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
     * @return mixed|null|string
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
     * @return Mapping[]|mixed|null
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
     * @return int|mixed|null
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
     * @return int|mixed|null
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
     * @return mixed|null|string
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
     * @return mixed|null|string
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
     * @return mixed|null|string
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
     * @return mixed|null|string
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
     * @return bool|mixed|null
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
     * @return bool|mixed|null
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
     * @return mixed|null|string
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
     * @return bool|mixed|null
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
     * @return bool|mixed|null
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
     * @return int|mixed|null
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
     * @return int|mixed|null
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
     * @return bool|mixed|null
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
     * @return bool|mixed|null
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
