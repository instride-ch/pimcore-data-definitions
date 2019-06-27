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
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

interface ImportDefinitionInterface extends DataDefinitionInterface
{
    /**
     * @return mixed
     */
    public function getLoader();

    /**
     * @param string $loader
     */
    public function setLoader($loader);

    /**
     * @return mixed
     */
    public function getObjectPath();

    /**
     * @param string $objectPath
     */
    public function setObjectPath($objectPath);

    /**
     * @return mixed
     */
    public function getCleaner();

    /**
     * @param string $cleaner
     */
    public function setCleaner($cleaner);

    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return mixed
     */
    public function getFilter();

    /**
     * @param string $filter
     */
    public function setFilter($filter);

    /**
     * @return mixed
     */
    public function getRenameExistingObjects();

    /**
     * @param boolean $renameExistingObjects
     */
    public function setRenameExistingObjects($renameExistingObjects);

    /**
     * @return mixed
     */
    public function getRelocateExistingObjects();

    /**
     * @param boolean $relocateExistingObjects
     */
    public function setRelocateExistingObjects($relocateExistingObjects);

    /**
     * @return bool
     */
    public function getOmitMandatoryCheck();

    /**
     * @param bool $omitMandatoryCheck
     */
    public function setOmitMandatoryCheck($omitMandatoryCheck);

    /**
     * @return mixed
     */
    public function getSkipNewObjects();

    /**
     * @param bool $skipNewObjects
     */
    public function setSkipNewObjects($skipNewObjects);

    /**
     * @return mixed
     */
    public function getSkipExistingObjects();

    /**
     * @param bool $skipExistingObjects
     */
    public function setSkipExistingObjects($skipExistingObjects);

    /**
     * @return boolean
     */
    public function getForceLoadObject();

    /**
     * @param $forceLoadObject
     */
    public function setForceLoadObject($forceLoadObject);

    /**
     * @return mixed
     */
    public function getCreateVersion();

    /**
     * @param boolean $createVersion
     */
    public function setCreateVersion($createVersion);
}

class_alias(ImportDefinitionInterface::class, 'ImportDefinitionsBundle\Model\ImportDefinitionInterface');
