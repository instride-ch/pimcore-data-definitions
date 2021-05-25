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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface DataDefinitionInterface extends ResourceInterface
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
     * @param string $class
     */
    public function setClass(string $class);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return MappingInterface[]
     */
    public function getMapping();

    /**
     * @param MappingInterface[] $mapping
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
    public function getRunner();

    /**
     * @param string $runner
     */
    public function setRunner($runner);

    /**
     * @return mixed
     */
    public function getStopOnException();

    /**
     * @param boolean $stopOnException
     */
    public function setStopOnException($stopOnException);

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
}
