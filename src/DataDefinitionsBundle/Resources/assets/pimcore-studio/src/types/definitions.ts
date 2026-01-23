/**
 * Data Definitions Types
 */

export interface ImportDefinition {
  id: number
  name: string
  provider?: string
  class?: string
  objectPath?: string
  key?: string
  filter?: string
  renameExistingObjects?: boolean
  relocateExistingObjects?: boolean
  createVersion?: boolean
  stopOnException?: boolean
  skipExistingObjects?: boolean
  skipNewObjects?: boolean
  omitMandatoryCheck?: boolean
  failureNotificationDocument?: number
  successNotificationDocument?: number
  mapping?: ImportMapping[]
  loader?: string
  cleaner?: string
  runner?: string
  persister?: string
  fetcherConfig?: Record<string, any>
}

export interface ExportDefinition {
  id: number
  name: string
  provider?: string
  class?: string
  filter?: string
  mapping?: ExportMapping[]
  runner?: string
  fetcherConfig?: Record<string, any>
}

export interface ImportMapping {
  fromColumn: string
  toColumn: string
  primaryIdentifier?: boolean
  interpreter?: string
  interpreterConfig?: Record<string, any>
  setter?: string
  setterConfig?: Record<string, any>
}

export interface ExportMapping {
  fromColumn: string
  toColumn: string
  interpreter?: string
  interpreterConfig?: Record<string, any>
  getter?: string
  getterConfig?: Record<string, any>
}

export interface DefinitionConfig {
  providers: string[]
  loaders: string[]
  filters: string[]
  interpreters: string[]
  setters: string[]
  getters: string[]
  cleaners: string[]
  runners: string[]
  persisters: string[]
  fetchers: string[]
  importRules?: {
    conditions: string[]
    actions: string[]
  }
}
