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
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Instride\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;
use Instride\Bundle\DataDefinitionsBundle\Importer\AsyncImporterInterface;
use Instride\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

final class ImportAsyncCommand extends AbstractCommand
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected DefinitionRepository $repository,
        protected AsyncImporterInterface $importer
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
