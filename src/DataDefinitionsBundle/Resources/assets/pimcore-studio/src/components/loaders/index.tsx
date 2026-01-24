/**
 * Loader Configuration Components
 *
 * Dynamically renders the appropriate config panel based on loader type.
 * Uses the registry system to allow external plugins to register their own components.
 *
 * Note: Built-in loader (primary_key) doesn't require configuration.
 * This registry exists for external plugins that may add configurable loaders.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

export interface LoaderConfigProps {
  type: string
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export const LoaderConfig: React.FC<LoaderConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<LoaderConfigProps>>(
    dataDefinitionsServiceIds.loaderConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}
