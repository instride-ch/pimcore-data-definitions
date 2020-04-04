<?php


namespace Wvision\Bundle\DataDefinitionsBundle\ProcessManager;


use Carbon\Carbon;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use ProcessManagerBundle\Factory\ProcessFactoryInterface;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\ProcessInterface;
use ProcessManagerBundle\ProcessManagerBundle;
use ProcessManagerBundle\Repository\ProcessRepository;
use Wvision\Bundle\DataDefinitionsBundle\Event\DefinitionEventInterface;
use Wvision\Bundle\DataDefinitionsBundle\Event\ExportDefinitionEvent;
use Wvision\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;

abstract class AbstractProcessManagerListener
{
    const PROCESS_TYPE = "data_definitions";

    const PROCESS_NAME = "Data Definitions";

    /**
     * @var ProcessInterface
     */
    protected $process;

    /**
     * @var ProcessFactoryInterface
     */
    protected $processFactory;

    /**
     * @var ProcessLogger
     */
    protected $processLogger;

    /**
     * @var ServiceRegistryInterface
     */
    protected $providerRegistry;

    /**
     * @var ProcessRepository
     */
    protected $repository;

    /**
     * AbstractProcessManagerListener constructor.
     * @param FactoryInterface $processFactory
     * @param ProcessLogger $processLogger
     * @param ProcessRepository $repository
     */
    public function __construct(
        FactoryInterface $processFactory,
        ProcessLogger $processLogger,
        ProcessRepository $repository
    ) {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
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
     * @param DefinitionEventInterface $event
     */
    public function onTotalEvent(DefinitionEventInterface $event)
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
     * @param DefinitionEventInterface $event
     */
    public function onStatusEvent(DefinitionEventInterface $event)
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
    public function onFinishedEvent(DefinitionEventInterface $event)
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
