/**
 * Data Definitions Bundle - Pimcore Studio Plugin
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    DDCL
 */

import { container } from '@pimcore/studio-ui-bundle'
import { ConfigRegistry } from '../registry/base-config-registry'
import { dataDefinitionsServiceIds } from '../registry/service-ids'

// Import shared components
import { NoConfig } from '../components/shared/NoConfig'

// Import interpreter config components
import type { InterpreterConfigProps } from '../components/interpreters'
import { DefaultValueConfig } from '../components/interpreters/DefaultValueConfig'
import { ExpressionConfig } from '../components/interpreters/ExpressionConfig'
import { MappingConfig } from '../components/interpreters/MappingConfig'
import { TwigConfig } from '../components/interpreters/TwigConfig'
import { TypeCastingConfig } from '../components/interpreters/TypeCastingConfig'
import { CarbonConfig } from '../components/interpreters/CarbonConfig'
import { HrefConfig } from '../components/interpreters/HrefConfig'
import { ObjectResolverConfig } from '../components/interpreters/ObjectResolverConfig'
import { AssetByPathConfig } from '../components/interpreters/AssetByPathConfig'
import { AssetUrlConfig } from '../components/interpreters/AssetUrlConfig'
import { QuantityValueConfig } from '../components/interpreters/QuantityValueConfig'
import { SpecificObjectConfig } from '../components/interpreters/SpecificObjectConfig'
import { MetadataConfig } from '../components/interpreters/MetadataConfig'
import { DefinitionConfig as DefinitionInterpreterConfig } from '../components/interpreters/DefinitionConfig'
import { ConditionalConfig } from '../components/interpreters/ConditionalConfig'
import { IteratorConfig } from '../components/interpreters/IteratorConfig'
import { NestedConfig } from '../components/interpreters/NestedConfig'
import { ImportRuleConfig } from '../components/interpreters/ImportRuleConfig'
// CoreShop interpreter configs
import { CoreShopPriceConfig } from '../components/interpreters/coreshop/CoreShopPriceConfig'
import { CoreShopMoneyConfig } from '../components/interpreters/coreshop/CoreShopMoneyConfig'
import { CoreShopStoresConfig } from '../components/interpreters/coreshop/CoreShopStoresConfig'

// Import provider config components
import type { ProviderConfigProps } from '../components/providers'
import { CsvProviderConfig } from '../components/providers/CsvProviderConfig'
import { ExcelProviderConfig } from '../components/providers/ExcelProviderConfig'
import { JsonProviderConfig } from '../components/providers/JsonProviderConfig'
import { XmlProviderConfig } from '../components/providers/XmlProviderConfig'
import { SqlProviderConfig } from '../components/providers/SqlProviderConfig'
import { ExternalSqlProviderConfig } from '../components/providers/ExternalSqlProviderConfig'
import { RawProviderConfig } from '../components/providers/RawProviderConfig'

// Import setter config components
import type { SetterConfigProps } from '../components/setters'
import { LocalizedFieldSetterConfig } from '../components/setters/LocalizedFieldConfig'
import { ObjectBrickSetterConfig } from '../components/setters/ObjectBrickConfig'
import { FieldCollectionSetterConfig } from '../components/setters/FieldCollectionConfig'
import { ClassificationStoreSetterConfig } from '../components/setters/ClassificationStoreConfig'
// CoreShop setter configs
import { CoreShopStorePriceSetterConfig } from '../components/setters/coreshop/CoreShopStorePriceConfig'
import { CoreShopStoreValuesSetterConfig } from '../components/setters/coreshop/CoreShopStoreValuesConfig'

// Import getter config components
import type { GetterConfigProps } from '../components/getters'
import { LocalizedFieldGetterConfig } from '../components/getters/LocalizedFieldConfig'
import { ObjectBrickGetterConfig } from '../components/getters/ObjectBrickConfig'
import { FieldCollectionGetterConfig } from '../components/getters/FieldCollectionConfig'
import { ClassificationStoreGetterConfig } from '../components/getters/ClassificationStoreConfig'
// CoreShop getter configs
import { CoreShopStorePriceGetterConfig } from '../components/getters/coreshop/CoreShopStorePriceConfig'
import { CoreShopStoreValuesGetterConfig } from '../components/getters/coreshop/CoreShopStoreValuesConfig'

// Import cleaner, loader, fetcher config components
import type { CleanerConfigProps } from '../components/cleaners'
import type { LoaderConfigProps } from '../components/loaders'
import type { FetcherConfigProps } from '../components/fetchers'
import { ObjectsFetcherConfig } from '../components/fetchers/ObjectsFetcherConfig'

// Import rule action and condition config components
import type { RuleActionConfigProps, RuleConditionConfigProps } from '../components/rules'
import { ExpressionActionConfig } from '../components/rules/actions/ExpressionActionConfig'
import { ObjectActionConfig } from '../components/rules/actions/ObjectActionConfig'
import { ExpressionConditionConfig } from '../components/rules/conditions/ExpressionConditionConfig'

/**
 * Registry module - initializes all config registries and registers built-in components.
 * External plugins can retrieve these registries via container.get() and register their own components.
 */
export const DataDefinitionsRegistryModule = {
  onInit(): void {
    // Initialize interpreter registry
    const interpreterRegistry = new ConfigRegistry<InterpreterConfigProps>()
    // Interpreters with configuration
    interpreterRegistry.register('default_value', DefaultValueConfig)
    interpreterRegistry.register('expression', ExpressionConfig)
    interpreterRegistry.register('mapping', MappingConfig)
    interpreterRegistry.register('twig', TwigConfig)
    interpreterRegistry.register('type_casting', TypeCastingConfig)
    interpreterRegistry.register('carbon', CarbonConfig)
    interpreterRegistry.register('href', HrefConfig)
    interpreterRegistry.register('multi_href', HrefConfig)
    interpreterRegistry.register('object_resolver', ObjectResolverConfig)
    interpreterRegistry.register('asset_by_path', AssetByPathConfig)
    interpreterRegistry.register('asset_url', AssetUrlConfig)
    interpreterRegistry.register('assets_url', AssetUrlConfig)
    interpreterRegistry.register('quantity_value', QuantityValueConfig)
    interpreterRegistry.register('specific_object', SpecificObjectConfig)
    interpreterRegistry.register('metadata', MetadataConfig)
    interpreterRegistry.register('definition', DefinitionInterpreterConfig)
    interpreterRegistry.register('conditional', ConditionalConfig)
    interpreterRegistry.register('iterator', IteratorConfig)
    interpreterRegistry.register('nested', NestedConfig)
    interpreterRegistry.register('import_rule', ImportRuleConfig)
    // Interpreters without configuration (NoConfigurationType in backend)
    interpreterRegistry.register('checkbox', NoConfig)
    interpreterRegistry.register('donotsetonempty', NoConfig)
    interpreterRegistry.register('external_image', NoConfig)
    interpreterRegistry.register('link', NoConfig)
    // CoreShop interpreters
    interpreterRegistry.register('coreshop_currency', NoConfig)
    interpreterRegistry.register('coreshop_money', CoreShopMoneyConfig)
    interpreterRegistry.register('coreshop_price', CoreShopPriceConfig)
    interpreterRegistry.register('coreshop_stores', CoreShopStoresConfig)
    container.bind(dataDefinitionsServiceIds.interpreterConfigRegistry).toConstantValue(interpreterRegistry)

    // Initialize provider registry (for import)
    const providerRegistry = new ConfigRegistry<ProviderConfigProps>()
    providerRegistry.register('csv', CsvProviderConfig)
    providerRegistry.register('excel', ExcelProviderConfig)
    providerRegistry.register('json', JsonProviderConfig)
    providerRegistry.register('xml', XmlProviderConfig)
    providerRegistry.register('sql', SqlProviderConfig)
    providerRegistry.register('external_sql', ExternalSqlProviderConfig)
    providerRegistry.register('externalsql', ExternalSqlProviderConfig)
    providerRegistry.register('raw', RawProviderConfig)
    container.bind(dataDefinitionsServiceIds.providerConfigRegistry).toConstantValue(providerRegistry)

    // Initialize export provider registry (same components for now, can be extended)
    const exportProviderRegistry = new ConfigRegistry<ProviderConfigProps>()
    exportProviderRegistry.register('csv', CsvProviderConfig)
    exportProviderRegistry.register('excel', ExcelProviderConfig)
    exportProviderRegistry.register('json', JsonProviderConfig)
    exportProviderRegistry.register('xml', XmlProviderConfig)
    exportProviderRegistry.register('sql', SqlProviderConfig)
    exportProviderRegistry.register('external_sql', ExternalSqlProviderConfig)
    exportProviderRegistry.register('externalsql', ExternalSqlProviderConfig)
    exportProviderRegistry.register('raw', RawProviderConfig)
    container.bind(dataDefinitionsServiceIds.exportProviderConfigRegistry).toConstantValue(exportProviderRegistry)

    // Initialize setter registry
    const setterRegistry = new ConfigRegistry<SetterConfigProps>()
    // Setters with configuration
    setterRegistry.register('localizedfield', LocalizedFieldSetterConfig)
    setterRegistry.register('objectbrick', ObjectBrickSetterConfig)
    setterRegistry.register('fieldcollection', FieldCollectionSetterConfig)
    setterRegistry.register('classificationstore', ClassificationStoreSetterConfig)
    // Setters without configuration (NoConfigurationType in backend)
    setterRegistry.register('key', NoConfig)
    setterRegistry.register('object_type', NoConfig)
    setterRegistry.register('relation', NoConfig)
    // CoreShop setters
    setterRegistry.register('coreshop_store_price', CoreShopStorePriceSetterConfig)
    setterRegistry.register('coreshop_store_values', CoreShopStoreValuesSetterConfig)
    container.bind(dataDefinitionsServiceIds.setterConfigRegistry).toConstantValue(setterRegistry)

    // Initialize getter registry
    const getterRegistry = new ConfigRegistry<GetterConfigProps>()
    // Getters with configuration
    getterRegistry.register('localizedfield', LocalizedFieldGetterConfig)
    getterRegistry.register('objectbrick', ObjectBrickGetterConfig)
    getterRegistry.register('fieldcollection', FieldCollectionGetterConfig)
    getterRegistry.register('classificationstore', ClassificationStoreGetterConfig)
    // Getters without configuration (classificationstore_field uses NoConfigurationType)
    getterRegistry.register('classificationstore_field', NoConfig)
    // CoreShop getters
    getterRegistry.register('coreshop_store_price', CoreShopStorePriceGetterConfig)
    getterRegistry.register('coreshop_store_values', CoreShopStoreValuesGetterConfig)
    container.bind(dataDefinitionsServiceIds.getterConfigRegistry).toConstantValue(getterRegistry)

    // Initialize cleaner registry (built-in cleaners have no configuration)
    const cleanerRegistry = new ConfigRegistry<CleanerConfigProps>()
    // Built-in cleaners don't need config, but we register them for completeness
    cleanerRegistry.register('deleter', NoConfig)
    cleanerRegistry.register('none', NoConfig)
    cleanerRegistry.register('reference_cleaner', NoConfig)
    cleanerRegistry.register('unpublisher', NoConfig)
    container.bind(dataDefinitionsServiceIds.cleanerConfigRegistry).toConstantValue(cleanerRegistry)

    // Initialize filter registry (no built-in filters, extensible by plugins)
    const filterRegistry = new ConfigRegistry<CleanerConfigProps>()
    container.bind(dataDefinitionsServiceIds.filterConfigRegistry).toConstantValue(filterRegistry)

    // Initialize loader registry (built-in loader has no configuration)
    const loaderRegistry = new ConfigRegistry<LoaderConfigProps>()
    loaderRegistry.register('primary_key', NoConfig)
    container.bind(dataDefinitionsServiceIds.loaderConfigRegistry).toConstantValue(loaderRegistry)

    // Initialize persister registry (built-in persister has no configuration)
    const persisterRegistry = new ConfigRegistry<LoaderConfigProps>()
    persisterRegistry.register('persister', NoConfig)
    container.bind(dataDefinitionsServiceIds.persisterConfigRegistry).toConstantValue(persisterRegistry)

    // Initialize runner registry (no built-in runners, extensible by plugins)
    const runnerRegistry = new ConfigRegistry<LoaderConfigProps>()
    container.bind(dataDefinitionsServiceIds.runnerConfigRegistry).toConstantValue(runnerRegistry)

    // Initialize export runner registry (no built-in export runners, extensible by plugins)
    const exportRunnerRegistry = new ConfigRegistry<LoaderConfigProps>()
    container.bind(dataDefinitionsServiceIds.exportRunnerConfigRegistry).toConstantValue(exportRunnerRegistry)

    // Initialize fetcher registry
    const fetcherRegistry = new ConfigRegistry<FetcherConfigProps>()
    fetcherRegistry.register('objects', ObjectsFetcherConfig)
    container.bind(dataDefinitionsServiceIds.fetcherConfigRegistry).toConstantValue(fetcherRegistry)

    // Initialize rule action registry
    const ruleActionRegistry = new ConfigRegistry<RuleActionConfigProps>()
    ruleActionRegistry.register('expression', ExpressionActionConfig)
    ruleActionRegistry.register('object', ObjectActionConfig)
    container.bind(dataDefinitionsServiceIds.ruleActionConfigRegistry).toConstantValue(ruleActionRegistry)

    // Initialize rule condition registry
    const ruleConditionRegistry = new ConfigRegistry<RuleConditionConfigProps>()
    ruleConditionRegistry.register('expression', ExpressionConditionConfig)
    container.bind(dataDefinitionsServiceIds.ruleConditionConfigRegistry).toConstantValue(ruleConditionRegistry)
  }
}
