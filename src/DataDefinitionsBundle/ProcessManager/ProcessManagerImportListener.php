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
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\ProcessManager;

use Carbon\Carbon;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use ProcessManagerBundle\Factory\ProcessFactoryInterface;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\Process;
use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\ProcessManagerBundle;
use ProcessManagerBundle\Repository\ProcessRepository;
use Wvision\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;

final class ProcessManagerImportListener
{
    /**
     * @var ProcessInterface
     */
    private $process;

    /**
     * @var ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * @var ProcessLogger
     */
    private $processLogger;

    /**
     * @var ProcessRepository
     */
    private $repository;

    /**
     * @param FactoryInterface $processFactory
     * @param ProcessLogger    $processLogger
     */
    public function __construct(FactoryInterface $processFactory, ProcessLogger $processLogger, ProcessRepository $repository)
    {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
        $this->repository = $repository;
    }

    /**
     * @return ProcessInterface
     */
    public function getProcess(): ProcessInterface
    {
        return $this->process;
    }

    /**
     * @param ImportDefinitionEvent $event
     */
    public function onTotalEvent(ImportDefinitionEvent $event)
    {
        if (null === $this->process) {
            $date = Carbon::now();

            $this->process = $this->processFactory->createProcess(
                sprintf(
                    'ImportDefinitions (%s): %s',
                    $date->formatLocalized('%A %d %B %Y'),
                    $event->getDefinition()->getName()
                ),
                'import_definitions',
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

    public function onProgressEvent()
    {
        if ($this->process) {
            if ($this->process->getStoppable()) {
                $this->process = $this->repository->find($this->process->getId());
            }
            $this->process->progress();
            $this->process->save();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_PROGRESS);
        }
    }

    /**
     * @param ImportDefinitionEvent $event
     */
    public function onStatusEvent(ImportDefinitionEvent $event)
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
     * @param ImportDefinitionEvent $event
     */
    public function onFinishedEvent(ImportDefinitionEvent $event)
    {
        if ($this->process) {
            if ($this->process->getStatus() == ProcessManagerBundle::STATUS_RUNNING) {
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED);
                $this->process->save();
            }
            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_FINISHED.$event->getSubject());
        }
    }
}


