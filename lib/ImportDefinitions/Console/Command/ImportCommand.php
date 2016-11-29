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
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions\Console\Command;

use Carbon\Carbon;
use ImportDefinitions\Model\Definition;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportCommand extends AbstractCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('importdefinitions:run')
            ->setDescription('Run Import Definition')
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
     * execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Zend_Session::$_unitTestEnabled = true;

        $this->disableLogging();

        $params = $input->getOption('params');
        $definition = Definition::getById($input->getOption("definition"));
        $progress = null;
        $process = null;

        if (!$definition instanceof Definition) {
            throw new \Exception("Definition not found");
        }

        \Zend_Registry::set("Zend_Locale", new \Zend_Locale("en"));

        \Pimcore::getEventManager()->attach("importdefinitions.status", function(\Zend_EventManager_Event $e) use ($output, &$progress, &$process)  {
            if($progress instanceof ProgressBar) {
                $progress->setMessage($e->getTarget());
                $progress->display();
            }

            if(class_exists('\ProcessManager\Model\Process')) {
                if($process instanceof \ProcessManager\Model\Process) {
                    $process->getLogger()->info($e->getTarget());
                    $process->setMessage($e->getTarget());
                    $process->save();
                }
            }
        });

        \Pimcore::getEventManager()->attach("importdefinitions.total", function(\Zend_EventManager_Event $e) use ($output, $definition, &$progress, &$process) {
            $progress = new ProgressBar($output, $e->getTarget());
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) %message%');
            $progress->start();

            if(class_exists('\ProcessManager\Model\Process')) {
                $date = Carbon::now();
                $process = new \ProcessManager\Model\Process();
                $process->setName(sprintf('ImportDefinitions (%s): %s', $date->formatLocalized("%A %d %B %Y"), $definition->getName()));
                $process->setTotal($e->getTarget());
                $process->setMessage('Loading');
                $process->setProgress(0);
                $process->save();
            }
        });

        \Pimcore::getEventManager()->attach("importdefinitions.object.finished", function(\Zend_EventManager_Event $e) use ($output, &$progress, &$process) {
            if($progress instanceof ProgressBar) {
                $progress->advance();
            }

            if(class_exists('\ProcessManager\Model\Process')) {
                if($process instanceof \ProcessManager\Model\Process) {
                    $process->progress();
                }
            }
        });

        \Pimcore::getEventManager()->attach("importdefinitions.finished", function(\Zend_EventManager_Event $e) use ($output, &$progress, &$process) {
            if($progress instanceof ProgressBar) {
                $progress->finish();
                $output->writeln("");
            }

            $output->writeln("Import finished!");
            $output->writeln("");
        });

        $definition->doImport(\Zend_Json::decode($params));
    }
}
