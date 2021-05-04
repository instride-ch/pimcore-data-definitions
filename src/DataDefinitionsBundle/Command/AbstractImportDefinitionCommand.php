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

use CoreShop\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wvision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

abstract class AbstractImportDefinitionCommand extends AbstractCommand
{
    protected MetadataInterface $metadata;
    protected DefinitionRepository $repository;
    protected EntityManagerInterface $manager;
    protected ResourceFormFactoryInterface $resourceFormFactory;

    public function __construct(
        MetadataInterface $metadata,
        DefinitionRepository $repository,
        EntityManagerInterface $manager,
        ResourceFormFactoryInterface $resourceFormFactory
    ) {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->manager = $manager;
        $this->resourceFormFactory = $resourceFormFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $type = $this->getType();

        $this
            ->setName(sprintf('data-definitions:definition:import:%s', strtolower($type)))
            ->setDescription(sprintf('Create a %s Definition.', $type))
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                sprintf('Path to %s Definition JSON export file', $type)
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getPath();

        $jsonContent = file_get_contents($path);
        $data = json_decode($jsonContent, true);

        try {
            $definition = $this->repository->findByName($data['name']);
        } catch (InvalidArgumentException $e) {
            $class = $this->repository->getClassName();
            $definition = new $class();
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
     * @throws InvalidArgumentException
     *
     */
    protected function getPath(): string
    {
        $path = $this->input->getArgument('path');
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException('File does not exist');
        }

        return $path;
    }

    /**
     * Get type
     *
     * @return string
     */
    abstract protected function getType(): string;
}
