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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

class DataDefinitionsExtension extends AbstractModelExtension
{
    /**
     * @return string
     */
    public function getAlias()
    {
        return 'import_definitions';
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

        $bcResources = $config['resources'];
        $bcResources['definition'] = $bcResources['import_definition'];
        unset($bcResources['import_definition']);

        $this->registerResources('import_definitions', $config['driver'], $bcResources, $container);

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
    }
}
