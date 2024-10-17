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

namespace Instride\Bundle\DataDefinitionsBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Instride\Bundle\DataDefinitionsBundle\Cleaner\CleanerInterface;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportProviderRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportRunnerRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FetcherRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\GetterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\LoaderRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\PersisterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\Fetcher\FetcherInterface;
use Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Instride\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;
use Instride\Bundle\DataDefinitionsBundle\Loader\LoaderInterface;
use Instride\Bundle\DataDefinitionsBundle\Persister\PersisterInterface;
use Instride\Bundle\DataDefinitionsBundle\Provider\ExportProviderInterface;
use Instride\Bundle\DataDefinitionsBundle\Provider\ImportProviderInterface;
use Instride\Bundle\DataDefinitionsBundle\Runner\ExportRunnerInterface;
use Instride\Bundle\DataDefinitionsBundle\Runner\RunnerInterface;
use Instride\Bundle\DataDefinitionsBundle\Setter\SetterInterface;
use Pimcore\Config\LocationAwareConfigRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

class DataDefinitionsExtension extends AbstractModelExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerResources('data_definitions', $config['driver'], $config['resources'], $container);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('CoreShopCoreBundle', $bundles)) {
            $config['pimcore_admin']['js']['coreshop_interpreter_price'] = '/bundles/datadefinitions/pimcore/js/coreshop/interpreter/price.js';
            $config['pimcore_admin']['js']['coreshop_interpreter_stores'] = '/bundles/datadefinitions/pimcore/js/coreshop/interpreter/stores.js';
            $config['pimcore_admin']['js']['coreshop_interpreter_money'] = '/bundles/datadefinitions/pimcore/js/coreshop/interpreter/money.js';
            $config['pimcore_admin']['js']['coreshop_setter_storePrice'] = '/bundles/datadefinitions/pimcore/js/coreshop/setter/storePrice.js';
            $config['pimcore_admin']['js']['coreshop_getter_storePrice'] = '/bundles/datadefinitions/pimcore/js/coreshop/getter/storePrice.js';
            $config['pimcore_admin']['js']['coreshop_setter_store_values'] = '/bundles/datadefinitions/pimcore/js/coreshop/setter/storeValues.js';
            $config['pimcore_admin']['js']['coreshop_getter_store_values'] = '/bundles/datadefinitions/pimcore/js/coreshop/getter/storeValues.js';

            $loader->load('coreshop.yml');
        }

        $loader->load('services.yml');

        if (class_exists(\GuzzleHttp\Psr7\HttpFactory::class)) {
            $loader->load('guzzle_psr7.yml');
        }

        if (array_key_exists('ProcessManagerBundle', $bundles)) {
            $config['pimcore_admin']['js']['process_manager_import'] = '/bundles/datadefinitions/pimcore/js/process_manager/import_definitions.js';
            $config['pimcore_admin']['js']['process_manager_export'] = '/bundles/datadefinitions/pimcore/js/process_manager/export_definitions.js';
            $config['pimcore_admin']['js']['process_manager_export_contextmenu'] = '/bundles/datadefinitions/pimcore/js/process_manager/export_contextmenu.js';
            $config['pimcore_admin']['js']['process_manager_export_search'] = '/bundles/datadefinitions/pimcore/js/process_manager/export_search.js';
            $loader->load('process_manager.yml');
        }

        $this->registerPimcoreResources('data_definitions', $config['pimcore_admin'], $container);

        $container
            ->registerForAutoconfiguration(CleanerInterface::class)
            ->addTag(CleanerRegistryCompilerPass::CLEANER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(ExportProviderInterface::class)
            ->addTag(ExportProviderRegistryCompilerPass::EXPORT_PROVIDER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(ExportRunnerInterface::class)
            ->addTag(ExportRunnerRegistryCompilerPass::EXPORT_RUNNER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(FetcherInterface::class)
            ->addTag(FetcherRegistryCompilerPass::FETCHER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(FilterInterface::class)
            ->addTag(FilterRegistryCompilerPass::FILTER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(GetterInterface::class)
            ->addTag(GetterRegistryCompilerPass::GETTER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(InterpreterInterface::class)
            ->addTag(InterpreterRegistryCompilerPass::INTERPRETER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(LoaderInterface::class)
            ->addTag(LoaderRegistryCompilerPass::LOADER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(ImportProviderInterface::class)
            ->addTag(ProviderRegistryCompilerPass::IMPORT_PROVIDER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(RunnerInterface::class)
            ->addTag(RunnerRegistryCompilerPass::RUNNER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(SetterInterface::class)
            ->addTag(SetterRegistryCompilerPass::SETTER_TAG)
        ;
        $container
            ->registerForAutoconfiguration(PersisterInterface::class)
            ->addTag(PersisterRegistryCompilerPass::PERSISTER_TAG)
        ;

        $container->setParameter('data_definitions.config_location', $config['config_location'] ?? []);

        $container->setParameter('data_definitions.import_definitions', $config['import_definitions']);
        $container->setParameter('data_definitions.export_definitions', $config['export_definitions']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        LocationAwareConfigRepository::loadSymfonyConfigFiles($container, 'data_definitions', 'export_definitions');
        LocationAwareConfigRepository::loadSymfonyConfigFiles($container, 'data_definitions', 'import_definitions');
    }
}
