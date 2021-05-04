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

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Command;

use Exception;
use InvalidArgumentException;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Wvision\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;
use Wvision\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

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

        $params = json_decode($input->getOption('params'), true);
        if (!$params['userId']) {
            $params['userId'] = 0;
        }

        try {
            $definition = $this->repository->find($input->getOption('definition'));
        } catch (InvalidArgumentException $e) {
            $definition = $this->repository->findByName($input->getOption('definition'));
        }
        $progress = null;
        $process = null;
        $countProgress = 0;
        $startTime = time();

        if (!$definition instanceof ImportDefinitionInterface) {
            throw new Exception('Import Definition not found');
        }

        $imStatus = function (ImportDefinitionEvent $e) use ($output, &$progress, &$countProgress, $startTime) {
            if ($progress instanceof ProgressBar) {
                $progress->setMessage($e->getSubject());
                $progress->display();
            }
            else {
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
