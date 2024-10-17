<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model;

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
     * @param bool $renameExistingObjects
     */
    public function setRenameExistingObjects($renameExistingObjects);

    /**
     * @return mixed
     */
    public function getRelocateExistingObjects();

    /**
     * @param bool $relocateExistingObjects
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
     * @return bool
     */
    public function getForceLoadObject();

    /**
     * @param bool $forceLoadObject
     */
    public function setForceLoadObject($forceLoadObject);

    /**
     * @return mixed
     */
    public function getCreateVersion();

    /**
     * @param bool $createVersion
     */
    public function setCreateVersion($createVersion);

    /**
     * @return mixed
     */
    public function getPersister();

    /**
     * @param string $persister
     */
    public function setPersister($persister);
}
