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

    /**
     * Get By Id
     *
     * @param int $id
     * @return Definition
     */
    public static function getById($id)
    {
        $definitionEntry = new static();
        $definitionEntry->setId((int)$id);
        $definitionEntry->getDao()->getById();

        return $definitionEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * {@inheritdoc}
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * {@inheritdoc}
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;
    }

    /**
     * {@inheritdoc}
     */
    public function getStopOnException()
    {
        return $this->stopOnException;
    }

    /**
     * {@inheritdoc}
     */
    public function setStopOnException($stopOnException)
    {
        $this->stopOnException = $stopOnException;
    }

    /**
     * {@inheritdoc}
     */
    public function getFailureNotificationDocument()
    {
        return $this->failureNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function setFailureNotificationDocument($failureNotificationDocument)
    {
        $this->failureNotificationDocument = $failureNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessNotificationDocument()
    {
        return $this->successNotificationDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccessNotificationDocument($successNotificationDocument)
    {
        $this->successNotificationDocument = $successNotificationDocument;
    }
}
