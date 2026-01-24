/**
 * Fetcher Configuration Components
 *
 * Dynamically renders the appropriate config panel based on fetcher type.
 * Uses the registry system to allow external plugins to register their own components.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

export interface FetcherConfigProps {
  type: string
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export const FetcherConfig: React.FC<FetcherConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<FetcherConfigProps>>(
    dataDefinitionsServiceIds.fetcherConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}

// Re-export individual configs
export * from './ObjectsFetcherConfig'
