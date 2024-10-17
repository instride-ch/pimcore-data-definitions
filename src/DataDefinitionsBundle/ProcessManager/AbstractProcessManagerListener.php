<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use Carbon\Carbon;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Event\DefinitionEventInterface;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\ProcessManagerBundle;
use ProcessManagerBundle\Repository\ProcessRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractProcessManagerListener
{
    public const PROCESS_TYPE = 'data_definitions';

    public const PROCESS_NAME = 'Data Definitions';

    private const PROCESS_PROGRESS_THROTTLE_SECONDS = 1;

    protected $process;

    protected $processFactory;

    protected $processLogger;

    protected $repository;

    protected $eventDispatcher;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastProgressAt;

    /**
     * @var int
     */
    protected $lastProgressStepsCount = 0;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastStatusAt;

    public function __construct(
        FactoryInterface $processFactory,
        ProcessLogger $processLogger,
        ProcessRepository $repository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return ProcessInterface|null
     */
    public function getProcess(): ProcessInterface
    {
        return $this->process;
    }

    public function onTotalEvent(DefinitionEventInterface $event): void
    {
        if (null === $this->process) {
            $date = Carbon::now();

            $this->process = $this->processFactory->createProcess(
                sprintf(
                    static::PROCESS_NAME . ' (%s): %s',
                    $date->formatLocalized('%A %d %B %Y'),
                    $event->getDefinition()->getName(),
                ),
                static::PROCESS_TYPE,
                'Loading',
                $event->getSubject(),
                0,
                -1,
                0,
                1,
                ProcessManagerBundle::STATUS_RUNNING,
            );
            $this->process->save();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_TOTAL . $event->getSubject());
        }
    }

    public function onProgressEvent(DefinitionEventInterface $event): void
    {
        if ($this->process) {
            $now = new \DateTimeImmutable();
            ++$this->lastProgressStepsCount;
            if ($this->lastProgressAt instanceof \DateTimeInterface) {
                $diff = $now->getTimestamp() - $this->lastProgressAt->getTimestamp();

                if (self::PROCESS_PROGRESS_THROTTLE_SECONDS > $diff) {
                    return;
                }
            }
            $this->lastProgressAt = $now;

            if ($this->process->getStoppable()) {
                $this->process = $this->repository->find($this->process->getId());
            }

            if ($this->process->getStatus() === ProcessManagerBundle::STATUS_STOPPING) {
                $this->eventDispatcher->dispatch('data_definitions.stop');
                $this->process->setStatus(ProcessManagerBundle::STATUS_STOPPED);
            }

            $this->process->progress($this->lastProgressStepsCount);
            $this->process->save();

            $this->lastProgressStepsCount = 0;

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_PROGRESS);
        }
    }

    public function onStatusEvent(DefinitionEventInterface $event): void
    {
        if ($this->process) {
            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_STATUS . $event->getSubject());

            $now = new \DateTimeImmutable();
            if ($this->lastStatusAt instanceof \DateTimeInterface) {
                $diff = $now->getTimestamp() - $this->lastStatusAt->getTimestamp();

                if (self::PROCESS_PROGRESS_THROTTLE_SECONDS > $diff) {
                    return;
                }
            }
            $this->lastStatusAt = $now;

            if ($this->process->getStoppable()) {
                $this->process = $this->repository->find($this->process->getId());
            }
            $this->process->setMessage($event->getSubject());
            $this->process->save();
        }
    }

    public function onFinishedEvent(DefinitionEventInterface $event): void
    {
        if ($this->process) {
            if ($this->process->getStatus() === ProcessManagerBundle::STATUS_RUNNING) {
                $this->process->setProgress($this->process->getTotal());
                $this->process->setMessage($event->getSubject());
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED);
                $this->process->setCompleted(time());
                $this->process->save();
            }
            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_FINISHED . $event->getSubject());
        }
    }

    public function onFailureEvent(DefinitionEventInterface $event): void
    {
        if ($this->process) {
            if ($event->getDefinition()->getStopOnException()) {
                $this->process->setStatus(ProcessManagerBundle::STATUS_FAILED);
            } else {
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED_WITH_EXCEPTIONS);
            }
            $this->process->setCompleted(time());
            $this->process->save();

            if (is_string($event->getSubject())) {
                $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_STATUS_ERROR . $event->getSubject());
            }
        }
    }
}
