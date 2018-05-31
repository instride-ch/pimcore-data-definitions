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

namespace ImportDefinitionsBundle\ProcessManager;

use Carbon\Carbon;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use ProcessManagerBundle\Model\ProcessInterface;
use ImportDefinitionsBundle\Event\ImportDefinitionEvent;

final class ProcessManagerListener
{
    /**
     * @var ProcessInterface
     */
    private $process;

    /**
     * @var FactoryInterface
     */
    private $processFactory;

    /**
     * @param FactoryInterface $processFactory
     */
    public function __construct(FactoryInterface $processFactory)
    {
        $this->processFactory = $processFactory;
    }

    /**
     * @param ImportDefinitionEvent $event
     */
    public function onTotalEvent(ImportDefinitionEvent $event)
    {
        if (null === $this->process) {
            $date = Carbon::now();

            $this->process = $this->processFactory->createNew();
            $this->process->setName(sprintf('ImportDefinitions (%s): %s', $date->formatLocalized('%A %d %B %Y'), $event->getDefinition()->getName()));
            $this->process->setTotal($event->getSubject());
            $this->process->setMessage('Loading');
            $this->process->setProgress(0);
            $this->process->save();
        }
    }

    public function onProgressEvent()
    {
        if ($this->process) {
            $this->process->progress();
            $this->process->save();
        }
    }

    /**
     * @param ImportDefinitionEvent $event
     */
    public function onStatusEvent(ImportDefinitionEvent $event)
    {
        if ($this->process) {
            $this->process->setMessage($event->getSubject());
            $this->process->save();
        }
    }
}