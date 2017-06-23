<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;

final class ImportDefinitionEvent extends Event
{
    /**
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * @var mixed
     */
    protected $subject;

    /**
     * @param DefinitionInterface $definition
     * @param mixed $subject
     */
    public function __construct(DefinitionInterface $definition, $subject = null)
    {
        $this->definition = $definition;
        $this->subject = $subject;
    }

    /**
     * @return DefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }
}