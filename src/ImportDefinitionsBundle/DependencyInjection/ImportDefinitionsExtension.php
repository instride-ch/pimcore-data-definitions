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

namespace ImportDefinitionsBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

class ImportDefinitionsExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerResources('import_definitions', $config['driver'], $config['resources'], $container);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('ProcessManagerBundle', $bundles)) {
            $config['pimcore_admin']['js']['process_manager'] = '/bundles/importdefinitions/pimcore/js/process_manager/import_definitions.js';
            $loader->load('process_manager.yml');
        }

        if (array_key_exists('CoreShopCoreBundle', $bundles)) {
            $config['pimcore_admin']['js']['coreshop_interpreter_price'] = '/bundles/importdefinitions/pimcore/js/coreshop/interpreter/price.js';
            $config['pimcore_admin']['js']['coreshop_interpreter_stores'] = '/bundles/importdefinitions/pimcore/js/coreshop/interpreter/stores.js';
            $config['pimcore_admin']['js']['coreshop_setter_storePrice'] = '/bundles/importdefinitions/pimcore/js/coreshop/setter/storePrice.js';

            $loader->load('coreshop.yml');
        }

        $this->registerPimcoreResources('import_definitions', $config['pimcore_admin'], $container);
        
        $loader->load('services.yml');
    }
}
