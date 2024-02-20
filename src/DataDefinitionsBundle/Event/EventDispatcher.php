<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    private SymfonyEventDispatcherInterface $eventDispatcher;

    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(DataDefinitionInterface $definition, $eventName, $subject = null, $params = []): void
    {
        $event = $this->getEvent($definition, $subject, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s%s', $eventName, isset($params['child']) && $params['child'] ? '.child' : '')
        );
    }

    private function getEvent(DataDefinitionInterface $definition, $subject = null, $params = []): ImportDefinitionEvent
    {
        return new ImportDefinitionEvent($definition, $subject, $params);
    }
}
