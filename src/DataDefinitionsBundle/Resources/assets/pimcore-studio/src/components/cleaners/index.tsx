/**
 * Cleaner Configuration Components
 *
 * Dynamically renders the appropriate config panel based on cleaner type.
 * Uses the registry system to allow external plugins to register their own components.
 *
 * Note: Built-in cleaners (deleter, none, reference_cleaner, unpublisher) don't require
 * configuration. This registry exists for external plugins that may add configurable cleaners.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

export interface CleanerConfigProps {
  type: string
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export const CleanerConfig: React.FC<CleanerConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<CleanerConfigProps>>(
    dataDefinitionsServiceIds.cleanerConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}
