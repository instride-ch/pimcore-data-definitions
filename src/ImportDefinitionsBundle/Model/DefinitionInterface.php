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

namespace ImportDefinitionsBundle\Model;

interface DefinitionInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getProvider();

    /**
     * @param string $provider
     */
    public function setProvider($provider);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration);

    /**
     * @return int
     */
    public function getClass();

    /**
     * @param int $class
     */
    public function setClass($class);

    /**
     * @return string
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
     * @return int
     */
    public function getCreationDate();

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate);

    /**
     * @return int
     */
    public function getModificationDate();

    /**
     * @param int $modificationDate
     */
    public function setModificationDate($modificationDate);

    /**
     * @return string
     */
    public function getObjectPath();

    /**
     * @param string $objectPath
     */
    public function setObjectPath($objectPath);

    /**
     * @return string
     */
    public function getCleaner();

    /**
     * @param string $cleaner
     */
    public function setCleaner($cleaner);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getFilter();

    /**
     * @param string $filter
     */
    public function setFilter($filter);

    /**
     * @return boolean
     */
    public function getRenameExistingObjects();

    /**
     * @param boolean $renameExistingObjects
     */
    public function setRenameExistingObjects($renameExistingObjects);

    /**
     * @return boolean
     */
    public function getRelocateExistingObjects();

    /**
     * @param boolean $relocateExistingObjects
     */
    public function setRelocateExistingObjects($relocateExistingObjects);

    /**
     * @return string
     */
    public function getRunner();

    /**
     * @param string $runner
     */
    public function setRunner($runner);

    /**
     * @return boolean
     */
    public function getCreateVersion();

    /**
     * @param boolean $createVersion
     */
    public function setCreateVersion($createVersion);

    /**
     * @return boolean
     */
    public function getStopOnException();

    /**
     * @param boolean $stopOnException
     */
    public function setStopOnException($stopOnException);

    /**
     * @return int
     */
    public function getFailureNotificationDocument();

    /**
     * @param int $failureNotificationDocument
     */
    public function setFailureNotificationDocument($failureNotificationDocument);

    /**
     * @return int
     */
    public function getSuccessNotificationDocument();

    /**
     * @param int $successNotificationDocument
     */
    public function setSuccessNotificationDocument($successNotificationDocument);

    /**
     * @return bool
     */
    public function getSkipNewObjects();

    /**
     * @param bool $skipNewObjects
     */
    public function setSkipNewObjects($skipNewObjects);

    /**
     * @return bool
     */
    public function getSkipExistingObjects();

    /**
     * @param bool $skipExistingObjects
     */
    public function setSkipExistingObjects($skipExistingObjects);
}