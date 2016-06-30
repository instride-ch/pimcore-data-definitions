<?php

namespace ImportDefinitions\Console\Command;

use ImportDefinitions\Model\Definition;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ListCommand extends AbstractCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('importdefinitions:list')
            ->setDescription('List Import Definition');
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
        $definitions = new Definition\Listing();

        foreach($definitions->getDefinitions() as $def) {
            $output->writeln($def->getId() . ": " . $def->getName() . " (".$def->getProvider().")");
        }
    }
}
