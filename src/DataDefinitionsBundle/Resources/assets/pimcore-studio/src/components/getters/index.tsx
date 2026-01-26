/**
 * Getter Configuration Components
 *
 * Dynamically renders the appropriate config panel based on getter type.
 * Uses the registry system to allow external plugins to register their own components.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import type { DefinitionConfig } from '../../types/definitions'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

export interface GetterConfigProps {
  type: string
  config: Record<string, any>
  definitionConfig?: DefinitionConfig
  fromColumnConfig?: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export const GetterConfig: React.FC<GetterConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<GetterConfigProps>>(
    dataDefinitionsServiceIds.getterConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}

// Re-export all individual configs
export * from './LocalizedFieldConfig'
export * from './ObjectBrickConfig'
export * from './FieldCollectionConfig'
export * from './ClassificationStoreConfig'
