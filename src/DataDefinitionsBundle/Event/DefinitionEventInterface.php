<?php


namespace Wvision\Bundle\DataDefinitionsBundle\Event;


use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

interface DefinitionEventInterface
{
    /**
     * @return ImportDefinitionInterface
     */
    public function getDefinition() : ImportDefinitionInterface;

    /**
     * @return mixed
     */
    public function getSubject();
}
