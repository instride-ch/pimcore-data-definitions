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
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @param string $provider
     */
    public function setProvider($provider);

    /**
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration);

    /**
     * @return int
     */
    public function getClass(): int;

    /**
     * @param int $class
     */
    public function setClass($class);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return Mapping[]
     */
    public function getMapping(): array;

    /**
     * @param Mapping[] $mapping
     */
    public function setMapping($mapping);

    /**
     * @return int
     */
    public function getCreationDate(): int;

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate);

    /**
     * @return int
     */
    public function getModificationDate(): int;

    /**
     * @param int $modificationDate
     */
    public function setModificationDate($modificationDate);

    /**
     * @return string
     */
    public function getObjectPath(): string;

    /**
     * @param string $objectPath
     */
    public function setObjectPath($objectPath);

    /**
     * @return string
     */
    public function getCleaner(): string;

    /**
     * @param string $cleaner
     */
    public function setCleaner($cleaner);

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getFilter(): string;

    /**
     * @param string $filter
     */
    public function setFilter($filter);

    /**
     * @return boolean
     */
    public function getRenameExistingObjects(): bool;

    /**
     * @param boolean $renameExistingObjects
     */
    public function setRenameExistingObjects($renameExistingObjects);

    /**
     * @return boolean
     */
    public function getRelocateExistingObjects(): bool;

    /**
     * @param boolean $relocateExistingObjects
     */
    public function setRelocateExistingObjects($relocateExistingObjects);

    /**
     * @return string
     */
    public function getRunner(): string;

    /**
     * @param string $runner
     */
    public function setRunner($runner);

    /**
     * @return boolean
     */
    public function getCreateVersion(): bool;

    /**
     * @param boolean $createVersion
     */
    public function setCreateVersion($createVersion);

    /**
     * @return boolean
     */
    public function getStopOnException(): bool;

    /**
     * @param boolean $stopOnException
     */
    public function setStopOnException($stopOnException);

    /**
     * @return int
     */
    public function getFailureNotificationDocument(): int;

    /**
     * @param int $failureNotificationDocument
     */
    public function setFailureNotificationDocument($failureNotificationDocument);

    /**
     * @return int
     */
    public function getSuccessNotificationDocument(): int;

    /**
     * @param int $successNotificationDocument
     */
    public function setSuccessNotificationDocument($successNotificationDocument);

    /**
     * @return bool
     */
    public function getSkipNewObjects(): bool;

    /**
     * @param bool $skipNewObjects
     */
    public function setSkipNewObjects($skipNewObjects);

    /**
     * @return bool
     */
    public function getSkipExistingObjects(): bool;

    /**
     * @param bool $skipExistingObjects
     */
    public function setSkipExistingObjects($skipExistingObjects);
}