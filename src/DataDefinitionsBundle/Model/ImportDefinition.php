<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
     * @var string
     */
    public $persister;

    public static function getById(int $name)
    {
        return static::getByName((string)$name);
    }

    public static function getByName(string $id)
    {
        $definitionEntry = new ImportDefinition();
        $definitionEntry->setId((int)$id);
        /**
         * @var \Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinition\Dao|\Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition\Dao
         */
        $dao = $definitionEntry->getDao();
        $dao->getByName($id);

        return $definitionEntry;
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
