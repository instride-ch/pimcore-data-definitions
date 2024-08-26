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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Command;

use Exception;
use InvalidArgumentException;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Exception\NotFoundException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Instride\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;
use Instride\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

final class ImportCommand extends AbstractCommand
{
    protected EventDispatcherInterface $eventDispatcher;
    protected DefinitionRepository $repository;
    protected ImporterInterface $importer;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DefinitionRepository $repository,
        ImporterInterface $importer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
        $this->importer = $importer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('data-definitions:import')
            ->setDescription('Run a Data Definition Import.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> runs a Data Definition Import.
EOT
            )
            ->addOption(
                'definition',
                'd',
                InputOption::VALUE_REQUIRED,
                'Import Definition ID or Name'
            )
            ->addOption(
                'params',
                'p',
                InputOption::VALUE_REQUIRED,
                'JSON Encoded Params'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventDispatcher = $this->eventDispatcher;

        $params = json_decode($input->getOption('params'), true, 512, JSON_THROW_ON_ERROR);
        $definitionId = $input->getOption('definition');

        if (!isset($params['userId'])) {
            $params['userId'] = 0;
        }

        $definition = null;

        try {
            if (filter_var($definitionId, FILTER_VALIDATE_INT)) {
                $definition = $this->repository->find($definitionId);
            } else {
                $definition = $this->repository->findByName($definitionId);
            }
        } catch (NotFoundException) {

        }

        if (!$definition instanceof ImportDefinitionInterface) {
            throw new Exception(sprintf('Import Definition with ID/Name "%s" not found', $definitionId));
        }

        $progress = null;
        $countProgress = 0;
        $startTime = time();

        $imStatus = function (ImportDefinitionEvent $e) use ($output, &$progress, &$countProgress, $startTime) {
            if ($progress instanceof ProgressBar) {
                $progress->setMessage($e->getSubject());
                $progress->display();
            } else {
                $output->writeln(
                    sprintf(
                        '%s (%s) (%s): %s',
                        $countProgress,
                        Helper::formatTime(time() - $startTime),
                        Helper::formatMemory(memory_get_usage(true)),
                        $e->getSubject()
                    )
                );
            }
        };

        $imTotal = function (ImportDefinitionEvent $e) use ($output, &$progress) {
            $progress = new ProgressBar($output, $e->getSubject());
            $progress->setFormat(
                ' %current%/%max% [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) %memory:6s%: %message%'
            );
            $progress->start();
        };

        $imProgress = function (ImportDefinitionEvent $e) use (&$progress, &$countProgress) {
            if ($progress instanceof ProgressBar) {
                $progress->advance();
            }

            $countProgress++;
        };

        $imFinished = function (ImportDefinitionEvent $e) use ($output, &$progress) {
            if ($progress instanceof ProgressBar) {
                $output->writeln('');
            }

            $output->writeln('Import finished!');
            $output->writeln('');
        };

        $imFailure = function (ImportDefinitionEvent $e) use ($output, &$progress) {
            if ($progress instanceof ProgressBar) {
                $output->writeln('');
            }

            $output->writeln('Import failed!');
            $output->writeln('');
        };

        $imSuccess = function (ImportDefinitionEvent $e) use ($output, &$progress) {
            if ($progress instanceof ProgressBar) {
                $output->writeln('');
            }

            $output->writeln('Import finished successfully!');
            $output->writeln('');
        };

        $eventDispatcher->addListener('data_definitions.import.status', $imStatus);
        $eventDispatcher->addListener('data_definitions.import.status.child', $imStatus);
        $eventDispatcher->addListener('data_definitions.import.total', $imTotal);
        $eventDispatcher->addListener('data_definitions.import.progress', $imProgress);
        $eventDispatcher->addListener('data_definitions.import.finished', $imFinished);

        $this->importer->doImport($definition, $params);

        $eventDispatcher->removeListener('data_definitions.import.status', $imStatus);
        $eventDispatcher->removeListener('data_definitions.import.status.child', $imStatus);
        $eventDispatcher->removeListener('data_definitions.import.total', $imTotal);
        $eventDispatcher->removeListener('data_definitions.import.progress', $imProgress);
        $eventDispatcher->removeListener('data_definitions.import.finished', $imFinished);

        return 0;
    }
}
