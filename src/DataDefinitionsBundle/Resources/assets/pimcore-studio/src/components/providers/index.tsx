/**
 * Provider Configuration Components
 *
 * Dynamically renders the appropriate config panel based on provider type.
 * Uses the registry system to allow external plugins to register their own components.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

export interface ProviderConfigProps {
  type: string
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export const ProviderConfig: React.FC<ProviderConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<ProviderConfigProps>>(
    dataDefinitionsServiceIds.providerConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}

// Re-export all individual configs
export * from './CsvProviderConfig'
export * from './ExcelProviderConfig'
export * from './JsonProviderConfig'
export * from './XmlProviderConfig'
export * from './SqlProviderConfig'
export * from './ExternalSqlProviderConfig'
export * from './RawProviderConfig'
