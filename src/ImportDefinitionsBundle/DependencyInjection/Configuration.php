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

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use ImportDefinitionsBundle\Controller\DefinitionController;
use ImportDefinitionsBundle\Form\Type\DefinitionType;
use ImportDefinitionsBundle\Model\Definition;
use ImportDefinitionsBundle\Model\DefinitionInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wvision_import_definitions');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_PIMCORE)->end()
            ->end()
        ;

        $this->addPimcoreResourcesSection($rootNode);
        $this->addModelsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('definition')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('definition')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Definition::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(DefinitionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(DefinitionController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(DefinitionType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
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
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('startup')->defaultValue('/bundles/importdefinitions/pimcore/js/startup.js')->end()
                            ->scalarNode('definition_panel')->defaultValue('/bundles/importdefinitions/pimcore/js/definition/panel.js')->end()
                            ->scalarNode('definition_item')->defaultValue('/bundles/importdefinitions/pimcore/js/definition/item.js')->end()
                            ->scalarNode('definition_config')->defaultValue('/bundles/importdefinitions/pimcore/js/definition/configDialog.js')->end()
                            ->scalarNode('provider_abstract')->defaultValue('/bundles/importdefinitions/pimcore/js/provider/abstractprovider.js')->end()
                            ->scalarNode('provider_csv')->defaultValue('/bundles/importdefinitions/pimcore/js/provider/csv.js')->end()
                            ->scalarNode('provider_sql')->defaultValue('/bundles/importdefinitions/pimcore/js/provider/sql.js')->end()
                            ->scalarNode('provider_external_sql')->defaultValue('/bundles/importdefinitions/pimcore/js/provider/externalSql.js')->end()
                            ->scalarNode('provider_json')->defaultValue('/bundles/importdefinitions/pimcore/js/provider/json.js')->end()
                            ->scalarNode('provider_xml')->defaultValue('/bundles/importdefinitions/pimcore/js/provider/xml.js')->end()
                            ->scalarNode('interpreter_abstract')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/abstract.js')->end()
                            ->scalarNode('interpreter_href')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/href.js')->end()
                            ->scalarNode('interpreter_multihref')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/multihref.js')->end()
                            ->scalarNode('interpreter_defaultvalue')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/defaultvalue.js')->end()
                            ->scalarNode('interpreter_specificobject')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/specificobject.js')->end()
                            ->scalarNode('interpreter_assetbypath')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/assetbypath.js')->end()
                            ->scalarNode('interpreter_asseturl')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/asseturl.js')->end()
                            ->scalarNode('interpreter_assetsurl')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/assetsurl.js')->end()
                            ->scalarNode('interpreter_quantityvalue')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/quantityvalue.js')->end()
                            ->scalarNode('interpreter_nested')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/nested.js')->end()
                            ->scalarNode('interpreter_nested_container')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/nestedcontainer.js')->end()
                            ->scalarNode('interpreter_empty')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/empty.js')->end()
                            ->scalarNode('interpreter_expression')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/expression.js')->end()
                            ->scalarNode('interpreter_object_resolver')->defaultValue('/bundles/importdefinitions/pimcore/js/interpreters/objectresolver.js')->end()
                            ->scalarNode('setter_abstract')->defaultValue('/bundles/importdefinitions/pimcore/js/setters/abstract.js')->end()
                            ->scalarNode('setter_fieldcollection')->defaultValue('/bundles/importdefinitions/pimcore/js/setters/fieldcollection.js')->end()
                            ->scalarNode('setter_objectbrick')->defaultValue('/bundles/importdefinitions/pimcore/js/setters/objectbrick.js')->end()
                            ->scalarNode('setter_classificationstore')->defaultValue('/bundles/importdefinitions/pimcore/js/setters/classificationstore.js')->end()
                            ->scalarNode('setter_localizedfield')->defaultValue('/bundles/importdefinitions/pimcore/js/setters/localizedfield.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('import_definition')->defaultValue('/bundles/importdefinitions/pimcore/css/importdefinition.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('sql')->defaultValue(['@ImportDefinitionsBundle/Resources/install/pimcore/sql/data.sql'])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
