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

use CoreShop\Component\Resource\Model\ResourceInterface;

interface DataDefinitionInterface extends ResourceInterface
{
    public function getId(): int|string|null;

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
     * @param bool $stopOnException
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
