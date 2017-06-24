<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

class ImportDefinitionsExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
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

        $this->registerPimcoreResources('import_definitions', $config['pimcore_admin'], $container);
        
        $loader->load('services.yml');
    }
}
