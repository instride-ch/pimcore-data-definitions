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

namespace ImportDefinitionsBundle\Event;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use ImportDefinitionsBundle\Model\DefinitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

interface EventDispatcherInterface
{
    /**
     * @param DefinitionInterface $definition
     * @param                     $eventName
     * @param string              $subject
     * @param array               $params
     * @return mixed
     */
    public function dispatch(DefinitionInterface $definition, $eventName, $subject = '', $params = []);
}
