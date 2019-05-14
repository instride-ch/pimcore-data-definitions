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
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractImportDefinitionCommand extends AbstractCommand
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
        $type = $this->getType();

        $this
            ->setName(sprintf('%s-definitions:definition:import', strtolower($type)))
            ->setDescription(sprintf('Create a %s Definition.', $type))
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                sprintf('Path to %s Definition JSON export file', $type)
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
            $definition = $this->repository->findByName($data['name']);
        } catch (\InvalidArgumentException $e) {
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
     * @throws \InvalidArgumentException
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

    /**
     * Get type
     *
     * @return string
     */
    abstract protected function getType();
}
