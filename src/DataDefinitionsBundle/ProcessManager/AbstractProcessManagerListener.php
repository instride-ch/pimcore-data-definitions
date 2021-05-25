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
 * @copyright  Copyright (c) 2020 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\ProcessManager;

use Carbon\Carbon;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use ProcessManagerBundle\Factory\ProcessFactoryInterface;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\ProcessManagerBundle;
use ProcessManagerBundle\Repository\ProcessRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Wvision\Bundle\DataDefinitionsBundle\Event\DefinitionEventInterface;

abstract class AbstractProcessManagerListener
{
    public const PROCESS_TYPE = 'data_definitions';
    public const PROCESS_NAME = 'Data Definitions';

    protected $process;
    protected $processFactory;
    protected $processLogger;
    protected $repository;
    protected $eventDispatcher;

    public function __construct(
        FactoryInterface $processFactory,
        ProcessLogger $processLogger,
        ProcessRepository $repository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return ProcessInterface|null
     */
    public function getProcess() : ProcessInterface
    {
        return $this->process;
    }

    /**
     * @param DefinitionEventInterface $event
     */
    public function onTotalEvent(DefinitionEventInterface $event) : void
    {
        if (null === $this->process) {
            $date = Carbon::now();

            $this->process = $this->processFactory->createProcess(
                sprintf(
                    static::PROCESS_NAME . ' (%s): %s',
                    $date->formatLocalized('%A %d %B %Y'),
                    $event->getDefinition()->getName()
                ),
                static::PROCESS_TYPE,
                'Loading',
                $event->getSubject(),
                0,
                -1,
                0,
                1,
                ProcessManagerBundle::STATUS_RUNNING

            );
            $this->process->save();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_TOTAL.$event->getSubject());
        }
    }

    /**
     * @return void
     */
    public function onProgressEvent() : void
    {
        if ($this->process) {
            if ($this->process->getStoppable()) {
                $this->process = $this->repository->find($this->process->getId());
            }

            if ($this->process->getStatus() === ProcessManagerBundle::STATUS_STOPPING) {
                $this->eventDispatcher->dispatch('data_definitions.stop');
                $this->process->setStatus(ProcessManagerBundle::STATUS_STOPPED);
            }

            $this->process->progress();
            $this->process->save();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_PROGRESS);
        }
    }

    /**
     * @param DefinitionEventInterface $event
     */
    public function onStatusEvent(DefinitionEventInterface $event) : void
    {
        if ($this->process) {
            if ($this->process->getStoppable()) {
                $this->process = $this->repository->find($this->process->getId());
            }
            $this->process->setMessage($event->getSubject());
            $this->process->save();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_STATUS.$event->getSubject());
        }
    }

    /**
     * @param DefinitionEventInterface $event
     */
    public function onFinishedEvent(DefinitionEventInterface $event) : void
    {
        if ($this->process) {
            if ($this->process->getStatus() === ProcessManagerBundle::STATUS_RUNNING) {
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED);
                $this->process->save();
            }
            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_FINISHED.$event->getSubject());
        }
    }

    /**
     * @param DefinitionEventInterface $event
     */
    public function onFailureEvent(DefinitionEventInterface $event) : void
    {
        if ($this->process) {
            if ($event->getDefinition()->getStopOnException()) {
                $this->process->setStatus(ProcessManagerBundle::STATUS_FAILED);
            } else {
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED_WITH_EXCEPTIONS);
            }
            $this->process->save();
        }
    }
}
