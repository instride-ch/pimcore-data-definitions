/**
 * DataDefinitions Type Definitions
 */

/**
 * Base Mapping Interface
 */
export interface BaseMapping {
  fromColumn?: string
  toColumn?: string
  interpreter?: string
  interpreterConfig?: Record<string, any>
  setter?: string
  setterConfig?: Record<string, any>
  getter?: string
  getterConfig?: Record<string, any>
}

/**
 * Import Mapping with Primary Identifier
 */
export interface ImportMapping extends BaseMapping {
  primaryIdentifier?: boolean
}

/**
 * Export Mapping
 */
export interface ExportMapping extends BaseMapping {
  label?: string
  group?: string
}

/**
 * Import Definition Entity
 */
export interface ImportDefinition {
  id?: number
  name: string
  provider?: string
  loader?: string
  class?: string
  objectPath?: string
  key?: string
  cleaner?: string
  persister?: string
  filter?: string
  runner?: string
  mapping?: ImportMapping[]
  configuration?: Record<string, any>
  relocateExistingObjects?: boolean
  renameExistingObjects?: boolean
  skipExistingObjects?: boolean
  skipNewObjects?: boolean
  createVersion?: boolean
  stopOnException?: boolean
  omitMandatoryCheck?: boolean
  forceLoadObject?: boolean
  failureNotificationDocument?: string
  successNotificationDocument?: string
  isWriteable?: boolean
  creationDate?: number
  modificationDate?: number
}

/**
 * Export Definition Entity
 */
export interface ExportDefinition {
  id?: number
  name: string
  provider?: string
  class?: string
  fetcher?: string
  fetcherConfig?: Record<string, any>
  runner?: string
  filter?: string
  mapping?: ExportMapping[]
  configuration?: Record<string, any>
  enableInheritance?: boolean
  fetchUnpublished?: boolean
  stopOnException?: boolean
  failureNotificationDocument?: string
  successNotificationDocument?: string
  isWriteable?: boolean
  creationDate?: number
  modificationDate?: number
}

/**
 * Definition Configuration (from get-config endpoint)
 * Note: API returns singular field names (interpreter, setter, etc.)
 */
export interface DefinitionConfig {
  providers?: string[]
  loaders?: string[]
  cleaner?: string[]
  persister?: string[]
  runner?: string[]
  filters?: string[]
  interpreter?: string[]  // API returns singular
  setter?: string[]       // API returns singular
  getter?: string[]       // API returns singular
  fetcher?: string[]      // API returns singular
  import_rules?: {
    conditions?: string[]
    actions?: string[]
  }
}

/**
 * Column Definition
 */
export interface ColumnDefinition {
  id?: string
  identifier: string
  label?: string
  fieldtype?: string
  group?: string
}

/**
 * Column Response (from get-columns endpoint)
 */
export interface ColumnResponse {
  fromColumns: ColumnDefinition[]
  toColumns: ColumnDefinition[]
  mapping: ImportMapping[] | ExportMapping[]
}

/**
 * Class Definition Response (from Pimcore API)
 */
export interface ClassDefinitionResponse {
  items?: Array<{ name: string; id: string }>
}

/**
 * Import Config (with interpreters, setters config options)
 */
export interface ImportConfig extends DefinitionConfig {
  classes?: Array<{ name: string }>
}

/**
 * Export Config (with interpreters, getters config options)
 */
export interface ExportConfig extends DefinitionConfig {
  classes?: Array<{ name: string }>
}
