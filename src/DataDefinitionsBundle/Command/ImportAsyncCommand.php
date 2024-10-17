<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Command;

use Exception;
use Instride\Bundle\DataDefinitionsBundle\Importer\AsyncImporterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;
use InvalidArgumentException;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ImportAsyncCommand extends AbstractCommand
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected DefinitionRepository $repository,
        protected AsyncImporterInterface $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('data-definitions:async-import')
            ->setDescription('Run a Data Definition Import Async.')
            ->addOption(
                'definition',
                'd',
                InputOption::VALUE_REQUIRED,
                'Import Definition ID or Name',
            )
            ->addOption(
                'params',
                'p',
                InputOption::VALUE_REQUIRED,
                'JSON Encoded Params',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = json_decode($input->getOption('params'), true);

        if (!isset($params['userId'])) {
            $params['userId'] = 0;
        }

        try {
            $definition = $this->repository->find($input->getOption('definition'));
        } catch (InvalidArgumentException $e) {
            $definition = $this->repository->findByName($input->getOption('definition'));
        }

        if (!$definition instanceof ImportDefinitionInterface) {
            throw new Exception('Import Definition not found');
        }

        $this->importer->doImportAsync($definition, $params);

        return 0;
    }
}
