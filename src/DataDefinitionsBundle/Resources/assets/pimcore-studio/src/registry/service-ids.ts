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

/**
 * Service IDs for the Data Definitions registries.
 * External plugins can use these to retrieve registries and add their own components.
 */
export const dataDefinitionsServiceIds = {
  // Import/Export component registries
  interpreterConfigRegistry: 'DataDefinitions/Registry/InterpreterConfig',
  providerConfigRegistry: 'DataDefinitions/Registry/ProviderConfig',
  exportProviderConfigRegistry: 'DataDefinitions/Registry/ExportProviderConfig',
  setterConfigRegistry: 'DataDefinitions/Registry/SetterConfig',
  getterConfigRegistry: 'DataDefinitions/Registry/GetterConfig',

  // Additional registries
  cleanerConfigRegistry: 'DataDefinitions/Registry/CleanerConfig',
  filterConfigRegistry: 'DataDefinitions/Registry/FilterConfig',
  loaderConfigRegistry: 'DataDefinitions/Registry/LoaderConfig',
  persisterConfigRegistry: 'DataDefinitions/Registry/PersisterConfig',
  runnerConfigRegistry: 'DataDefinitions/Registry/RunnerConfig',
  exportRunnerConfigRegistry: 'DataDefinitions/Registry/ExportRunnerConfig',
  fetcherConfigRegistry: 'DataDefinitions/Registry/FetcherConfig',

  // Import Rule registries
  ruleActionConfigRegistry: 'DataDefinitions/Registry/RuleActionConfig',
  ruleConditionConfigRegistry: 'DataDefinitions/Registry/RuleConditionConfig',
} as const

export type DataDefinitionsServiceIds = typeof dataDefinitionsServiceIds
