<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace WVision\Bundle\DataDefinitionsBundle\Event;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use WVision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param SymfonyEventDispatcherInterface $eventDispatcher
     */
    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(DefinitionInterface $definition, $eventName, $subject = '', $params = [])
    {
        $event = $this->getEvent($definition, $subject, $params);

        $this->eventDispatcher->dispatch(
            sprintf('%s%s', $eventName, isset($params['child']) && $params['child'] ? '.child' : ''),
            $event
        );
    }

    /**
     * @param DefinitionInterface $definition
     * @param string              $subject
     * @param array               $params
     * @return ImportDefinitionEvent
     */
    private function getEvent(DefinitionInterface $definition, $subject = '', $params = [])
    {
        return new ImportDefinitionEvent($definition, $subject, $params);
    }
}

class_alias(EventDispatcher::class, 'ImportDefinitionsBundle\Event\EventDispatcher');
