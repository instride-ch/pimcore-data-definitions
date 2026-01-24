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

// Registry classes and service IDs
export { ConfigRegistry } from './base-config-registry'
export { dataDefinitionsServiceIds } from './service-ids'
export type { DataDefinitionsServiceIds } from './service-ids'

// Re-export config prop types for external plugins
export type { InterpreterConfigProps } from '../components/interpreters'
export type { ProviderConfigProps } from '../components/providers'
export type { SetterConfigProps } from '../components/setters'
export type { GetterConfigProps } from '../components/getters'
export type { CleanerConfigProps } from '../components/cleaners'
export type { LoaderConfigProps } from '../components/loaders'
export type { FetcherConfigProps } from '../components/fetchers'
export type { RuleActionConfigProps, RuleConditionConfigProps } from '../components/rules'

// Re-export shared components for external plugins
export { NoConfig } from '../components/shared/NoConfig'
export type { NoConfigProps } from '../components/shared/NoConfig'
