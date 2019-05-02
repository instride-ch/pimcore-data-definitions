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

use CoreShop\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ImportDefinitionsBundle\Model\ExportDefinition;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateExportCommand extends AbstractCommand
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ResourceFormFactoryInterface
     */
    protected $resourceFormFactory;

    public function __construct(
        MetadataInterface $metadata,
        RepositoryInterface $repository,
        ObjectManager $manager,
        ResourceFormFactoryInterface $resourceFormFactory
    ) {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->manager = $manager;
        $this->resourceFormFactory = $resourceFormFactory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('export-definitions:create')
            ->setDescription('Create a Export Definition.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> creates a Export Definition.
EOT
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to Export Definition JSON export file'
            );
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getPath();

        $jsonContent = file_get_contents($path);
        $data = json_decode($jsonContent, true);

        try {
            $definition = ExportDefinition::getByName($data['name']);
        } catch (\InvalidArgumentException $e) {
            $definition = new ExportDefinition();
        }

        $form = $this->resourceFormFactory->create($this->metadata, $definition);
        $handledForm = $form->submit($data);

        if (!$handledForm->isValid()) {
            foreach ($handledForm->getErrors() as $error) {
                $this->writeError($error->getMessage());
            }
            return 1;
        }

        $definition = $form->getData();
        $this->manager->persist($definition);
        $this->manager->flush();

        return 0;
    }

    /**
     * Validate and return path to JSON file
     *
     * @return string
     */
    protected function getPath()
    {
        $path = $this->input->getArgument('path');
        if (!file_exists($path) || !is_readable($path)) {
            throw new \InvalidArgumentException('File does not exist');
        }

        return $path;
    }
}
