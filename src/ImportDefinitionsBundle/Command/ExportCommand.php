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

namespace ImportDefinitionsBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ImportDefinitionsBundle\Event\ImportDefinitionEvent;
use ImportDefinitionsBundle\Model\DefinitionInterface;

final class ExportCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('import-definitions:export')
            ->setDescription('Run a Export Definition.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> runs a Export Definitions and imports Objects.
EOT
            )
            ->addOption(
                'definition', 'd',
                InputOption::VALUE_REQUIRED,
                'Import Definition ID'
            )
            ->addOption(
                'params', 'p',
                InputOption::VALUE_REQUIRED,
                'JSON Encoded Params'
            );
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $params = $input->getOption('params');
        $definition = $this->getContainer()->get('import_definitions.repository.definition')->find($input->getOption('definition'));
        $progress = null;
        $process = null;

        if (!$definition instanceof DefinitionInterface) {
            throw new \Exception('Definition not found');
        }

        $imStatus = function (ImportDefinitionEvent $e) use ($output, &$progress, &$process)  {
            if ($progress instanceof ProgressBar) {
                $progress->setMessage($e->getSubject());
                $progress->display();
            }
        };

        $imTotal = function (ImportDefinitionEvent $e) use ($output, $definition, &$progress, &$process) {
            $progress = new ProgressBar($output, $e->getSubject());
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) %memory:6s%: %message%');
            $progress->start();
        };

        $imProgress = function (ImportDefinitionEvent $e) use ($output, &$progress, &$process) {
            if ($progress instanceof ProgressBar) {
                $progress->advance();
            }
        };

        $imFinished = function (ImportDefinitionEvent $e) use ($output, &$progress, &$process) {
            if ($progress instanceof ProgressBar) {
                $progress->finish();
                $output->writeln('');
            }

            $output->writeln('Import finished!');
            $output->writeln('');
        };

        $eventDispatcher->addListener('export_definition.status', $imStatus);
        $eventDispatcher->addListener('export_definition.total', $imTotal);
        $eventDispatcher->addListener('export_definition.progress', $imProgress);
        $eventDispatcher->addListener('export_definition.finished', $imFinished);

        $this->getContainer()->get('import_definition.exporter')->doExport($definition, json_decode($params, true));

        $eventDispatcher->removeListener('export_definition.status', $imStatus);
        $eventDispatcher->removeListener('export_definition.total', $imTotal);
        $eventDispatcher->removeListener('export_definition.progress', $imProgress);
        $eventDispatcher->removeListener('export_definition.finished', $imFinished);

        return 0;
    }
}
