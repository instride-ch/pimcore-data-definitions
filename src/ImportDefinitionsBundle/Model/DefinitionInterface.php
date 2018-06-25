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

use CoreShop\Component\Resource\Model\ResourceInterface;

interface DefinitionInterface extends ResourceInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getProvider();

    /**
     * @param string $provider
     */
    public function setProvider($provider);

    /**
     * @return mixed
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration);

    /**
     * @return mixed
     */
    public function getClass();

    /**
     * @param int $class
     */
    public function setClass($class);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return Mapping[]
     */
    public function getMapping();

    /**
     * @param Mapping[] $mapping
     */
    public function setMapping($mapping);

    /**
     * @return mixed
     */
    public function getCreationDate();

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate);

    /**
     * @return mixed
     */
    public function getModificationDate();

    /**
     * @param int $modificationDate
     */
    public function setModificationDate($modificationDate);

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
     * @return mixed
     */
    public function getRunner();

    /**
     * @param string $runner
     */
    public function setRunner($runner);

    /**
     * @return mixed
     */
    public function getCreateVersion();

    /**
     * @param boolean $createVersion
     */
    public function setCreateVersion($createVersion);

    /**
     * @return mixed
     */
    public function getStopOnException();

    /**
     * @param boolean $stopOnException
     */
    public function setStopOnException($stopOnException);

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
    public function getFailureNotificationDocument();

    /**
     * @param int $failureNotificationDocument
     */
    public function setFailureNotificationDocument($failureNotificationDocument);

    /**
     * @return mixed
     */
    public function getSuccessNotificationDocument();

    /**
     * @param int $successNotificationDocument
     */
    public function setSuccessNotificationDocument($successNotificationDocument);

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
}