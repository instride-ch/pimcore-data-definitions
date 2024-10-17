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

use Pimcore\Model\AbstractModel;

/**
 * @method bool isWriteable()
 * @method string getWriteTarget()
 * @method void save()
 * @method void delete()
 */
abstract class AbstractDataDefinition extends AbstractModel implements DataDefinitionInterface
{
    /**
     * @var int|string|null
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $provider;

    /**
     * @var string
     */
    public $class;

    /**
     * @var array
     */
    public $configuration;

    /**
     * @var int
     */
    public $creationDate;

    /**
     * @var int
     */
    public $modificationDate;

    /**
     * @var MappingInterface[]
     */
    public $mapping;

    /**
     * @var string
     */
    public $runner;

    /**
     * @var bool
     */
    public $stopOnException;

    /**
     * @var int
     */
    public $failureNotificationDocument;

    /**
     * @var int
     */
    public $successNotificationDocument;

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMapping()
    {
        return $this->mapping;
    }

    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    public function getRunner()
    {
        return $this->runner;
    }

    public function setRunner($runner)
    {
        $this->runner = $runner;
    }

    public function getStopOnException()
    {
        return $this->stopOnException;
    }

    public function setStopOnException($stopOnException)
    {
        $this->stopOnException = $stopOnException;
    }

    public function getFailureNotificationDocument()
    {
        return $this->failureNotificationDocument;
    }

    public function setFailureNotificationDocument($failureNotificationDocument)
    {
        $this->failureNotificationDocument = $failureNotificationDocument;
    }

    public function getSuccessNotificationDocument()
    {
        return $this->successNotificationDocument;
    }

    public function setSuccessNotificationDocument($successNotificationDocument)
    {
        $this->successNotificationDocument = $successNotificationDocument;
    }
}
