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

namespace Wvision\Bundle\DataDefinitionsBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Wvision\Bundle\DataDefinitionsBundle\Cleaner\CleanerInterface;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportProviderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportRunnerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FetcherRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\GetterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\LoaderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\Fetcher\FetcherInterface;
use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Loader\LoaderInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ExportProviderInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportProviderInterface;
use Wvision\Bundle\DataDefinitionsBundle\Runner\ExportRunnerInterface;
use Wvision\Bundle\DataDefinitionsBundle\Runner\RunnerInterface;
use Wvision\Bundle\DataDefinitionsBundle\Setter\SetterInterface;

class DataDefinitionsExtension extends AbstractModelExtension
{
    /**
     * @return string
     */
    public function getAlias()
    {
        return 'data_definitions';
    }


    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerResources('data_definitions', $config['driver'], $config['resources'], $container);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('ProcessManagerBundle', $bundles)) {
            $config['pimcore_admin']['js']['process_manager_import'] = '/bundles/datadefinitions/pimcore/js/process_manager/import_definitions.js';
            $config['pimcore_admin']['js']['process_manager_export'] = '/bundles/datadefinitions/pimcore/js/process_manager/export_definitions.js';
            $config['pimcore_admin']['js']['process_manager_export_contextmenu'] = '/bundles/datadefinitions/pimcore/js/process_manager/export_contextmenu.js';
            $loader->load('process_manager.yml');
        }

        if (array_key_exists('CoreShopCoreBundle', $bundles)) {
            $config['pimcore_admin']['js']['coreshop_interpreter_price'] = '/bundles/datadefinitions/pimcore/js/coreshop/interpreter/price.js';
            $config['pimcore_admin']['js']['coreshop_interpreter_stores'] = '/bundles/datadefinitions/pimcore/js/coreshop/interpreter/stores.js';
            $config['pimcore_admin']['js']['coreshop_setter_storePrice'] = '/bundles/datadefinitions/pimcore/js/coreshop/setter/storePrice.js';
            $config['pimcore_admin']['js']['coreshop_getter_storePrice'] = '/bundles/datadefinitions/pimcore/js/coreshop/getter/storePrice.js';
            $config['pimcore_admin']['js']['coreshop_setter_store_values'] = '/bundles/datadefinitions/pimcore/js/coreshop/setter/storeValues.js';
            $config['pimcore_admin']['js']['coreshop_getter_store_values'] = '/bundles/datadefinitions/pimcore/js/coreshop/getter/storeValues.js';

            $loader->load('coreshop.yml');
        }

        $this->registerPimcoreResources('data_definitions', $config['pimcore_admin'], $container);

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(CleanerInterface::class)
            ->addTag(CleanerRegistryCompilerPass::CLEANER_TAG);
        $container
            ->registerForAutoconfiguration(ExportProviderInterface::class)
            ->addTag(ExportProviderRegistryCompilerPass::EXPORT_PROVIDER_TAG);
        $container
            ->registerForAutoconfiguration(ExportRunnerInterface::class)
            ->addTag(ExportRunnerRegistryCompilerPass::EXPORT_RUNNER_TAG);
        $container
            ->registerForAutoconfiguration(FetcherInterface::class)
            ->addTag(FetcherRegistryCompilerPass::FETCHER_TAG);
        $container
            ->registerForAutoconfiguration(FilterInterface::class)
            ->addTag(FilterRegistryCompilerPass::FILTER_TAG);
        $container
            ->registerForAutoconfiguration(GetterInterface::class)
            ->addTag(GetterRegistryCompilerPass::GETTER_TAG);
        $container
            ->registerForAutoconfiguration(InterpreterInterface::class)
            ->addTag(InterpreterRegistryCompilerPass::INTERPRETER_TAG);
        $container
            ->registerForAutoconfiguration(LoaderInterface::class)
            ->addTag(LoaderRegistryCompilerPass::LOADER_TAG);
        $container
            ->registerForAutoconfiguration(ImportProviderInterface::class)
            ->addTag(ProviderRegistryCompilerPass::IMPORT_PROVIDER_TAG);
        $container
            ->registerForAutoconfiguration(RunnerInterface::class)
            ->addTag(RunnerRegistryCompilerPass::RUNNER_TAG);
        $container
            ->registerForAutoconfiguration(SetterInterface::class)
            ->addTag(SetterRegistryCompilerPass::SETTER_TAG);
    }
}
