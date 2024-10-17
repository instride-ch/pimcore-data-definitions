<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model;

/**
 * @method ImportDefinition\Dao getDao()
 */
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
     * @var bool
     */
    public $createVersion;

    /**
     * @var bool
     */
    public $omitMandatoryCheck;

    /**
     * @var bool
     */
    public $forceLoadObject = false;

    /**
     * @var string
     */
    public $persister;

    public static function getById(int $id): self
    {
        $definitionEntry = new self();
        $dao = $definitionEntry->getDao();
        $dao->getById((string) $id);

        return $definitionEntry;
    }

    public static function getByName(string $name): self
    {
        $definitionEntry = new self();
        $dao = $definitionEntry->getDao();
        $dao->getByName($name);

        return $definitionEntry;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function setLoader($loader)
    {
        $this->loader = $loader;
    }

    public function getObjectPath()
    {
        return $this->objectPath;
    }

    public function setObjectPath($objectPath)
    {
        $this->objectPath = $objectPath;
    }

    public function getCleaner()
    {
        return $this->cleaner;
    }

    public function setCleaner($cleaner)
    {
        $this->cleaner = $cleaner;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getRenameExistingObjects()
    {
        return $this->renameExistingObjects;
    }

    public function setRenameExistingObjects($renameExistingObjects)
    {
        $this->renameExistingObjects = $renameExistingObjects;
    }

    public function getRelocateExistingObjects()
    {
        return $this->relocateExistingObjects;
    }

    public function setRelocateExistingObjects($relocateExistingObjects)
    {
        $this->relocateExistingObjects = $relocateExistingObjects;
    }

    public function getCreateVersion()
    {
        return $this->createVersion;
    }

    public function setCreateVersion($createVersion)
    {
        $this->createVersion = $createVersion;
    }

    public function getOmitMandatoryCheck()
    {
        return $this->omitMandatoryCheck;
    }

    public function setOmitMandatoryCheck($omitMandatoryCheck)
    {
        $this->omitMandatoryCheck = $omitMandatoryCheck;
    }

    public function getSkipNewObjects()
    {
        return $this->skipNewObjects;
    }

    public function setSkipNewObjects($skipNewObjects)
    {
        $this->skipNewObjects = $skipNewObjects;
    }

    public function getSkipExistingObjects()
    {
        return $this->skipExistingObjects;
    }

    public function setSkipExistingObjects($skipExistingObjects)
    {
        $this->skipExistingObjects = $skipExistingObjects;
    }

    public function getForceLoadObject()
    {
        return $this->forceLoadObject;
    }

    public function setForceLoadObject($forceLoadObject)
    {
        $this->forceLoadObject = $forceLoadObject;
    }

    public function getPersister()
    {
        return $this->persister;
    }

    public function setPersister($persister)
    {
        $this->persister = $persister;
    }
}
