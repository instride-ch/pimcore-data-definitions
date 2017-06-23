<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wvision_import_definitions');

        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addPimcoreResourcesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('definition_panel')->defaultValue('/bundles/importdefinition/pimcore/js/definition/panel.js')->end()
                            ->scalarNode('definition_item')->defaultValue('/bundles/importdefinition/pimcore/js/definition/item.js')->end()
                            ->scalarNode('definition_config')->defaultValue('/bundles/importdefinition/pimcore/js/definition/configDialog.js')->end()
                            ->scalarNode('provider_abstract')->defaultValue('/bundles/importdefinition/pimcore/js/provider/abstractprovider.js')->end()
                            ->scalarNode('provider_csv')->defaultValue('/bundles/importdefinition/pimcore/js/provider/csv.js')->end()
                            ->scalarNode('provider_sql')->defaultValue('/bundles/importdefinition/pimcore/js/provider/sql.js')->end()
                            ->scalarNode('provider_json')->defaultValue('/bundles/importdefinition/pimcore/js/provider/json.js')->end()
                            ->scalarNode('provider_xml')->defaultValue('/bundles/importdefinition/pimcore/js/provider/xml.js')->end()
                            ->scalarNode('interpreter_abstract')->defaultValue('/bundles/coreshopcurrency/pimcore/js/interpreters/abstract.js')->end()
                            ->scalarNode('interpreter_href')->defaultValue('/bundles/importdefinition/pimcore/js/interpreters/href.js')->end()
                            ->scalarNode('interpreter_multihref')->defaultValue('/bundles/importdefinition/pimcore/js/interpreters/multihref.js')->end()
                            ->scalarNode('interpreter_defaultvalue')->defaultValue('/bundles/importdefinition/pimcore/js/interpreters/defaultvalue.js')->end()
                            ->scalarNode('interpreter_asseturl')->defaultValue('/bundles/importdefinition/pimcore/js/interpreters/asseturl.js')->end()
                            ->scalarNode('interpreter_assetsurl')->defaultValue('/bundles/importdefinition/pimcore/js/interpreters/assetsurl.js')->end()
                            ->scalarNode('interpreter_quantityvalue')->defaultValue('/bundles/importdefinition/pimcore/js/interpreters/quantityvalue.js')->end()
                            ->scalarNode('setter_abstract')->defaultValue('/bundles/importdefinition/pimcore/js/setters/abstract.js')->end()
                            ->scalarNode('setter_fieldcollection')->defaultValue('/bundles/importdefinition/pimcore/js/setters/fieldcollection.js')->end()
                            ->scalarNode('setter_objectbrick')->defaultValue('/bundles/importdefinition/pimcore/js/setters/objectbrick.js')->end()
                            ->scalarNode('setter_classificationstore')->defaultValue('/bundles/importdefinition/pimcore/js/setters/classificationstore.js')->end()
                            ->scalarNode('setter_localizedfield')->defaultValue('/bundles/importdefinition/pimcore/js/setters/localizedfield.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('import_definition')->defaultValue('/bundles/importdefinition/pimcore/css/importdefinition.css')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
