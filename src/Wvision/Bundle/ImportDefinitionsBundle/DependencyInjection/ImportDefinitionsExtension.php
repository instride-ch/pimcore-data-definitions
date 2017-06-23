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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerPimcoreResources('import_definitions', $config['pimcore_admin'], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
