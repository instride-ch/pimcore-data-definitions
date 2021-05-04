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

use Pimcore\Model\AbstractModel;

abstract class AbstractDataDefinition extends AbstractModel implements DataDefinitionInterface
{
    /**
     * @var int
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
     * @var boolean
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function setName($name)
    {
        $this->name = $name;
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
