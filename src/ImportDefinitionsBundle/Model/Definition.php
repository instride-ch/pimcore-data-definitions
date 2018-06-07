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
     * @var boolean
     */
    public $omitMandatoryCheck;

    /**
     * @var int
     */
    public $failureNotificationDocument;

    /**
     * @var int
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * {@inheritdoc}
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectPath()
    {
        return $this->objectPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectPath($objectPath)
    {
        $this->objectPath = $objectPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getCleaner()
    {
        return $this->cleaner;
    }

    /**
     * {@inheritdoc}
     */
    public function setCleaner($cleaner)
    {
        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenameExistingObjects()
    {
        return $this->renameExistingObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function setRenameExistingObjects($renameExistingObjects)
    {
        $this->renameExistingObjects = $renameExistingObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelocateExistingObjects()
    {
        return $this->relocateExistingObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function setRelocateExistingObjects($relocateExistingObjects)
    {
        $this->relocateExistingObjects = $relocateExistingObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * {@inheritdoc}
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateVersion()
    {
        return $this->createVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreateVersion($createVersion)
    {
        $this->createVersion = $createVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getStopOnException()
    {
        return $this->stopOnException;
    }

    /**
     * {@inheritdoc}
     */
    public function setStopOnException($stopOnException)
    {
        $this->stopOnException = $stopOnException;
    }

    /**
     * {@inheritdoc}
     */
    public function getOmitMandatoryCheck()
    {
        return $this->omitMandatoryCheck;
    }

    /**
     * {@inheritdoc}
     */
    public function setOmitMandatoryCheck($omitMandatoryCheck)
    {
        $this->omitMandatoryCheck = $omitMandatoryCheck;
    }

    /**
     * {@inheritdoc}
     */
    public function getFailureNotificationDocument()
    {
        return $this->failureNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function setFailureNotificationDocument($failureNotificationDocument)
    {
        $this->failureNotificationDocument = $failureNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessNotificationDocument()
    {
        return $this->successNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccessNotificationDocument($successNotificationDocument)
    {
        $this->successNotificationDocument = $successNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function getSkipNewObjects()
    {
        return $this->skipNewObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function setSkipNewObjects($skipNewObjects)
    {
        $this->skipNewObjects = $skipNewObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getSkipExistingObjects()
    {
        return $this->skipExistingObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function setSkipExistingObjects($skipExistingObjects)
    {
        $this->skipExistingObjects = $skipExistingObjects;
    }
}
