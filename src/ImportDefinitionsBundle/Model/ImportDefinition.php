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

class ImportDefinition extends AbstractDataDefinition implements ImportDefinitionInterface
{
    /**
     * @var string
     */
    public $loader;

    /**
     * @var string
     */
    public $objectPath;

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
     * @var boolean
     */
    public $createVersion;

    /**
     * @var boolean
     */
    public $omitMandatoryCheck;

    /**
     * @var boolean
     */
    public $forceLoadObject = false;

    /**
     * {@inheritdoc}
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * {@inheritdoc}
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;
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

    /**
     * {@inheritdoc}
     */
    public function getForceLoadObject()
    {
        return $this->forceLoadObject;
    }

    /**
     * {@inheritdoc}
     */
    public function setForceLoadObject($forceLoadObject)
    {
        $this->forceLoadObject = $forceLoadObject;
    }
}
