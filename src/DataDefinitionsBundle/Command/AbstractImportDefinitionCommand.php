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

use CoreShop\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use CoreShop\Bundle\ResourceBundle\Pimcore\ObjectManager;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Instride\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;
use InvalidArgumentException;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractImportDefinitionCommand extends AbstractCommand
{
    protected MetadataInterface $metadata;

    protected DefinitionRepository $repository;

    protected ObjectManager $manager;

    protected ResourceFormFactoryInterface $resourceFormFactory;

    public function __construct(
        MetadataInterface $metadata,
        DefinitionRepository $repository,
        ObjectManager $manager,
        ResourceFormFactoryInterface $resourceFormFactory,
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
                sprintf('Path to %s Definition JSON export file', $type),
            )
        ;
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
     * @throws InvalidArgumentException
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
     */
    abstract protected function getType(): string;
}
