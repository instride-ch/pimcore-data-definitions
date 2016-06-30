<?php

namespace ImportDefinitions\Console\Command;

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
            ->setName('importdefinitions:import')
            ->setDescription('Import Definition')
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
        $params = $input->getOption('params');
        $definition = Definition::getById($input->getOption("definition"));

        if(!$definition instanceof Definition) {
            throw new \Exception("Definition not found");
        }

        \Zend_Registry::set("Zend_Locale", new \Zend_Locale("en"));

        $definition->doImport(\Zend_Json::decode($params));
    }
}
