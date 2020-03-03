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
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\Model\Asset;
use ProcessManagerBundle\Factory\ProcessFactoryInterface;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\ProcessManagerBundle;
use ProcessManagerBundle\Repository\ProcessRepository;
use Wvision\Bundle\DataDefinitionsBundle\Event\ExportDefinitionEvent;

final class ProcessManagerExportListener
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
     * @var ServiceRegistryInterface
     */
    private $providerRegistry;

    /**
     * @var ProcessRepository
     */
    private $repository;

    /**
     * @param FactoryInterface $processFactory
     * @param ProcessLogger $processLogger
     * @param ServiceRegistryInterface $providerRegistry
     */
    public function __construct(
        FactoryInterface $processFactory,
        ProcessLogger $processLogger,
        ServiceRegistryInterface $providerRegistry,
        ProcessRepository $repository
    ) {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
        $this->providerRegistry = $providerRegistry;
        $this->repository = $repository;
    }

    /**
     * @return ProcessInterface|null
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param ExportDefinitionEvent $event
     */
    public function onTotalEvent(ExportDefinitionEvent $event)
    {
        if (null === $this->process) {
            $date = Carbon::now();

            $this->process = $this->processFactory->createProcess(
                sprintf(
                    'Export Definitions (%s): %s',
                    $date->formatLocalized('%A %d %B %Y'),
                    $event->getDefinition()->getName()
                ),
                'export_definitions',
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
     * @param ExportDefinitionEvent $event
     */
    public function onStatusEvent(ExportDefinitionEvent $event)
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
     * @param ExportDefinitionEvent $event
     */
    public function onFinishedEvent(ExportDefinitionEvent $event)
    {
        if (null !== $this->process) {
            if ($this->process->getStatus() == ProcessManagerBundle::STATUS_RUNNING) {
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED);
                $this->process->save();
            }
            $definition = $event->getDefinition();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_FINISHED.$event->getSubject());

            $provider = $this->providerRegistry->get($definition->getProvider());

            if ($provider instanceof ArtifactGenerationProviderInterface) {
                if (method_exists($this->process, 'setArtifact')) {
                    $artifact = $provider->generateArtifact(
                        $definition->getConfiguration(),
                        $definition,
                        $event->getParams()
                    );

                    if ($artifact instanceof Asset) {
                        $this->process->setArtifact($artifact);
                        $this->process->save();
                    }
                }
            }
        }
    }
}


