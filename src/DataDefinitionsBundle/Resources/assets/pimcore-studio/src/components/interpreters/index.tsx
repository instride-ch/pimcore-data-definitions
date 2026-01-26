/**
 * Interpreter Configuration Components
 *
 * Dynamically renders the appropriate config panel based on interpreter type.
 * Uses the registry system to allow external plugins to register their own components.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import type { DefinitionConfig } from '../../types/definitions'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

export interface InterpreterConfigProps {
  type: string
  config: Record<string, any>
  definitionConfig?: DefinitionConfig
  toColumnConfig?: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export const InterpreterConfig: React.FC<InterpreterConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<InterpreterConfigProps>>(
    dataDefinitionsServiceIds.interpreterConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}

// Re-export all individual configs
export * from './DefaultValueConfig'
export * from './ExpressionConfig'
export * from './MappingConfig'
export * from './TwigConfig'
export * from './TypeCastingConfig'
export * from './CarbonConfig'
export * from './HrefConfig'
export * from './ObjectResolverConfig'
export * from './AssetByPathConfig'
export * from './AssetUrlConfig'
export * from './QuantityValueConfig'
export * from './SpecificObjectConfig'
export * from './MetadataConfig'
export * from './DefinitionConfig'
export * from './ConditionalConfig'
export * from './IteratorConfig'
export * from './NestedConfig'
export * from './ImportRuleConfig'
