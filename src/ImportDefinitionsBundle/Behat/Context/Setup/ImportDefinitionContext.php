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

namespace ImportDefinitionsBundle\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Pimcore\ObjectManager;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use ImportDefinitionsBundle\Behat\Service\SharedStorageInterface;
use ImportDefinitionsBundle\Importer\ImporterInterface;
use ImportDefinitionsBundle\Model\ImportDefinitionInterface;
use ImportDefinitionsBundle\Model\ImportMapping;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Form\FormFactoryInterface;

final class ImportDefinitionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var ImporterInterface
     */
    private $importer;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormTypeRegistryInterface
     */
    private $providerFormRegistry;

    /**
     * @param SharedStorageInterface    $sharedStorage
     * @param FactoryInterface          $factory
     * @param ObjectManager             $manager
     * @param ImporterInterface         $importer
     * @param FormFactoryInterface      $formFactory
     * @param FormTypeRegistryInterface $providerFormRegistry
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $factory,
        ObjectManager $manager,
        ImporterInterface $importer,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $providerFormRegistry
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->factory = $factory;
        $this->manager = $manager;
        $this->importer = $importer;
        $this->formFactory = $formFactory;
        $this->providerFormRegistry = $providerFormRegistry;
    }

    /**
     * @Given /^there is a import-definition "([^"]+)"$/
     * @Given /^there is a import-definition "([^"]+)" for (definition)$/
     */
    public function thereIsAImportDefinition($name, ClassDefinition $definition = null)
    {
        /**
         * @var ImportDefinitionInterface $importDefinition
         */
        $importDefinition = $this->factory->createNew();
        $importDefinition->setName($name);


        if (null !== $definition) {
            $importDefinition->setClass($definition->getName());
        }

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) provider is "([^"]+)"$/
     * @Given /^the (import-definitions) provider is "([^"]+)" with the configuration:/
     */
    public function theImportDefinitionsProviderIs(
        ImportDefinitionInterface $importDefinition,
        $provider,
        TableNode $tableNode = null
    ) {
        $importDefinition->setProvider($provider);

        if (null !== $tableNode) {
            $config = $this->processTableConfiguration($this->providerFormRegistry, $provider, $tableNode);
            $importDefinition->setConfiguration($config);
        }

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) loader is "([^"]+)"$/
     */
    public function theImportDefinitionsLoaderIs(ImportDefinitionInterface $importDefinition, $loader)
    {
        $importDefinition->setLoader($loader);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) object-path is "([^"]+)"$/
     */
    public function theImportDefinitionsObjectPathIs(ImportDefinitionInterface $importDefinition, $objectPath)
    {
        $importDefinition->setObjectPath($objectPath);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) cleaner is "([^"]+)"$/
     */
    public function theImportDefinitionsCleanerIs(ImportDefinitionInterface $importDefinition, $cleaner)
    {
        $importDefinition->setCleaner($cleaner);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) key is "([^"]+)"$/
     */
    public function theImportDefinitionsKeyIs(ImportDefinitionInterface $importDefinition, $key)
    {
        $importDefinition->setKey($key);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) filter is "([^"]+)"$/
     */
    public function theImportDefinitionsFilterIs(ImportDefinitionInterface $importDefinition, $filter)
    {
        $importDefinition->setFilter($filter);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) renames existing objects$/
     */
    public function theImportDefinitionsRenamesExistingObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setRenameExistingObjects(true);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) does not rename existing objects$/
     */
    public function theImportDefinitionsDoesNotRenameExistingObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setRenameExistingObjects(false);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) relocates existing objects$/
     */
    public function theImportDefinitionsRelocatesExistingObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setRelocateExistingObjects(true);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) does not relocate existing objects$/
     */
    public function theImportDefinitionsDoesNotRelocateExistingObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setRelocateExistingObjects(false);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) omits the mandatory check$/
     */
    public function theImportDefinitionsOmitsTheMandatoryCheck(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setOmitMandatoryCheck(true);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) does not omit the mandatory check$/
     */
    public function theImportDefinitionsDosNotOmitTheMandatoryCheck(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setOmitMandatoryCheck(false);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) skips new objects$/
     */
    public function theImportDefinitionsSkipsNewObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setSkipNewObjects(true);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) does not skip new objects$/
     */
    public function theImportDefinitionsDoesNotSkipNewObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setSkipNewObjects(false);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) skips existing objects$/
     */
    public function theImportDefinitionsSkipsExistingObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setSkipExistingObjects(true);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) does not skip existing objects$/
     */
    public function theImportDefinitionsDoesNotSkipExistingObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setSkipExistingObjects(false);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) force loads objects$/
     */
    public function theImportDefinitionsForceLoadsObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setForceLoadObject(true);

        $this->persist($importDefinition);
    }

    /**
     * @Given /^the (import-definitions) does not force load objects$/
     */
    public function theImportDefinitionsDoesNotForceLoadObjects(ImportDefinitionInterface $importDefinition)
    {
        $importDefinition->setForceLoadObject(false);

        $this->persist($importDefinition);
    }

    /**
     * @Given /the (import-definitions) mapping is:/
     */
    public function theIndexHasFollowingFields(ImportDefinitionInterface $definition, TableNode $table)
    {
        $hash = $table->getHash();

        $columns = [];

        foreach ($hash as $row) {
            /**
             * @var ImportMapping $mapping
             */
            $column = new ImportMapping();
            $column->setFromColumn($row['fromColumn']);
            $column->setToColumn($row['toColumn']);

            if (array_key_exists('primary', $row)) {
                $column->setPrimaryIdentifier(true);
            }

            if (array_key_exists('interpreter', $row)) {
                $column->setInterpreter($row['interpreter']);
                $column->setInterpreterConfig(json_decode($row['interpreterConfig'], true));
            }

            if (array_key_exists('setter', $row)) {
                $column->setSetter($row['setter']);
                $column->setSetterConfig(json_decode($row['setterConfig'], true));
            }

            $columns[] = $column;
        }

        $definition->setMapping($columns);

        $this->persist($definition);
    }

    /**
     * @Given there is a file :file with content:
     */
    public function thereIsACSVFileWithContent($path, PyStringNode $content)
    {
        file_put_contents(PIMCORE_PROJECT_ROOT.'/'.$path, $content);
    }

    /**
     * @Given /I run the (import-definitions) with params:/
     */
    public function IRunTheImportDefinition(ImportDefinitionInterface $importDefinition, TableNode $tableNode)
    {
        $config = [];

        foreach ($tableNode->getHash() as $row) {
            $config[$row['key']] = $row['value'];
        }

        $this->importer->doImport($importDefinition, $config);
    }

    /**
     * @param FormTypeRegistryInterface $formRegistry
     * @param                           $type
     * @param TableNode                 $tableNode
     * @return array
     */
    private function processTableConfiguration(FormTypeRegistryInterface $formRegistry, $type, TableNode $tableNode)
    {
        $config = [];

        foreach ($tableNode->getHash() as $row) {
            $config[$row['key']] = $row['value'];
        }

        $formType = $formRegistry->get($type, 'default');

        $form = $this->formFactory->createNamed('', $formType, null, ['csrf_protection' => false]);
        $form = $form->submit($config);

        if (!$form->isValid()) {
            throw new \InvalidArgumentException('Provided Configuration is invalid');
        }

        return $form->getData();
    }

    /**
     * @param ImportDefinitionInterface $importDefinition
     */
    private function persist(ImportDefinitionInterface $importDefinition)
    {
        $this->sharedStorage->set('import-definition', $importDefinition);

        $this->manager->persist($importDefinition);
        $this->manager->flush();
    }
}
