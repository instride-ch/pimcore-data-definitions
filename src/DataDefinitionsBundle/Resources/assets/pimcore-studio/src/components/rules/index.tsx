/**
 * Import Rules Configuration Components
 *
 * Provides configuration components for import rule actions and conditions.
 * Uses the registry system to allow external plugins to register their own components.
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { dataDefinitionsServiceIds } from '../../registry/service-ids'
import type { ConfigRegistry } from '../../registry/base-config-registry'

// Rule Action Config Props
export interface RuleActionConfigProps {
  type: string
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

// Rule Condition Config Props
export interface RuleConditionConfigProps {
  type: string
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

/**
 * Dynamic rule action configuration component
 * Renders the appropriate config panel based on action type
 */
export const RuleActionConfig: React.FC<RuleActionConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<RuleActionConfigProps>>(
    dataDefinitionsServiceIds.ruleActionConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}

/**
 * Dynamic rule condition configuration component
 * Renders the appropriate config panel based on condition type
 */
export const RuleConditionConfig: React.FC<RuleConditionConfigProps> = (props) => {
  const registry = container.get<ConfigRegistry<RuleConditionConfigProps>>(
    dataDefinitionsServiceIds.ruleConditionConfigRegistry
  )
  const Component = registry.get(props.type)

  if (!Component) {
    return null
  }

  return <Component {...props} />
}

// Re-export individual configs
export * from './actions'
export * from './conditions'
