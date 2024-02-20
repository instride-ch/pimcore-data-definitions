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

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

final class ListImportDefinitionsCommand extends AbstractCommand
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('data-definitions:list:imports')
            ->setDescription('List all Import Definitions.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> lists all Data Definitions for Imports.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importDefinitions = $this->repository->findAll();

        $data = [];

        /** @var ImportDefinitionInterface $definition */
        foreach ($importDefinitions as $definition) {
            $data[] = [
                $definition->getId(),
                $definition->getName(),
                $definition->getProvider(),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name', 'Provider'])
            ->setRows($data);
        $table->render();

        return 0;
    }
}
