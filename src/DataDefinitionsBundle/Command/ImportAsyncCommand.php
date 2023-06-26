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
use Wvision\Bundle\DataDefinitionsBundle\Importer\AsyncImporterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

final class ImportAsyncCommand extends AbstractCommand
{
    protected EventDispatcherInterface $eventDispatcher;
    protected DefinitionRepository $repository;
    protected ImporterInterface $importer;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DefinitionRepository $repository,
        AsyncImporterInterface $importer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
        $this->importer = $importer;

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
