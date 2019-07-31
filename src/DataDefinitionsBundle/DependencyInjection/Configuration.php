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

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Wvision\Bundle\DataDefinitionsBundle\Controller\ExportDefinitionController;
use Wvision\Bundle\DataDefinitionsBundle\Controller\ImportDefinitionController;
use Wvision\Bundle\DataDefinitionsBundle\Form\Type\ExportDefinitionType;
use Wvision\Bundle\DataDefinitionsBundle\Form\Type\ImportDefinitionType;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinition;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinition;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Repository;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wvision_data_definitions');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_PIMCORE)->end()
            ->end();

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
                        ->arrayNode('import_definition')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('data_definitions_import')->cannotBeOverwritten()
                            ->end()
                            ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->defaultValue(ImportDefinition::class)->cannotBeEmpty()->end()
                                    ->scalarNode('interface')->defaultValue(ImportDefinitionInterface::class)->cannotBeEmpty()->end()
                                    ->scalarNode('admin_controller')->defaultValue(ImportDefinitionController::class)->cannotBeEmpty()->end()
                                    ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                    ->scalarNode('repository')->defaultValue(Repository\DefinitionRepository::class)->cannotBeEmpty()->end()
                                    ->scalarNode('form')->defaultValue(ImportDefinitionType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('export_definition')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                    ->scalarNode('permission')->defaultValue('data_definitions_export')->cannotBeOverwritten()
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ExportDefinition::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ExportDefinitionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ExportDefinitionController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(Repository\DefinitionRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ExportDefinitionType::class)->cannotBeEmpty()->end()
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
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('css')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('sql')->defaultValue(['@DataDefinitionsBundle/Resources/install/pimcore/sql/data.sql'])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
