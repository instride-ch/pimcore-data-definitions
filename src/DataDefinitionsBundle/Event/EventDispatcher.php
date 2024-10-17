<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 *
 */

namespace Instride\Bundle\DataDefinitionsBundle\Event;

use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    private SymfonyEventDispatcherInterface $eventDispatcher;

    public function __construct(
        SymfonyEventDispatcherInterface $eventDispatcher,
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(DataDefinitionInterface $definition, $eventName, $subject = null, $params = []): void
    {
        $event = $this->getEvent($definition, $subject, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s%s', $eventName, isset($params['child']) && $params['child'] ? '.child' : ''),
        );
    }

    private function getEvent(DataDefinitionInterface $definition, $subject = null, $params = []): ImportDefinitionEvent
    {
        return new ImportDefinitionEvent($definition, $subject, $params);
    }
}
