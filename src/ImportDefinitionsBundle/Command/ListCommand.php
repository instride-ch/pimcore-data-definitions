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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ImportDefinitionsBundle\Model\DefinitionInterface;

final class ListCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('import-definitions:list')
            ->setDescription('List all Import Definition.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> lists all Import Definitions.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importDefinitions = $this->getContainer()->get('import_definitions.repository.definition')->findAll();

        $data = [];

        /** @var DefinitionInterface $definition */
        foreach ($importDefinitions as $definition) {
            $data[] = [
                $definition->getId(),
                $definition->getName(),
                $definition->getProvider()
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name', 'Provider'])
            ->setRows($data)
        ;
        $table->render();

        return 0;
    }
}
