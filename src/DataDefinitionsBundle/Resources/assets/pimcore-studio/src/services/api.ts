/**
 * DataDefinitions API Service
 */

import type { ImportDefinition, ExportDefinition, DefinitionConfig, ColumnResponse } from '../types/definitions'

interface EntityWithId {
  id?: number
}

/**
 * Base API class for entity operations
 */
abstract class BaseEntityApi<T extends EntityWithId> {
  protected abstract buildUrl(route: string): string

  async list(): Promise<T[]> {
    const url = this.buildUrl('/list')
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to fetch list')
    const data = await response.json()
    return data.data || data
  }

  async get(id: number): Promise<T> {
    const url = this.buildUrl(`/get?id=${id}`)
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to fetch entity')
    const data = await response.json()
    return data.data || data
  }

  async add(entity: Partial<T>): Promise<T> {
    const url = this.buildUrl('/add')
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(entity)
    })
    if (!response.ok) throw new Error('Failed to add entity')
    const data = await response.json()
    return data.data || data
  }

  async save(entity: T): Promise<T> {
    const url = this.buildUrl('/save')
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(entity)
    })
    if (!response.ok) throw new Error('Failed to save entity')
    const data = await response.json()
    return data.data || data
  }

  async delete(id: number): Promise<void> {
    const url = this.buildUrl(`/delete?id=${id}`)
    const response = await fetch(url, {
      method: 'DELETE',
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to delete entity')
  }
}

/**
 * Import Definition API
 */
export class ImportDefinitionApi extends BaseEntityApi<ImportDefinition> {
  protected buildUrl(route: string): string {
    return `/pimcore-studio/api/data_definitions/import_definitions${route}`
  }

  /**
   * Get import configuration (providers, loaders, interpreters, etc.)
   */
  async getConfig(): Promise<DefinitionConfig> {
    const url = this.buildUrl('/get-config')
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to fetch import config')
    return await response.json()
  }

  /**
   * Get columns for a definition (fromColumns, toColumns, existing mapping)
   */
  async getColumns(definitionId: number): Promise<ColumnResponse> {
    const url = this.buildUrl(`/get-columns?id=${definitionId}`)
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to fetch columns')
    return await response.json()
  }

  /**
   * Export a definition as JSON file
   */
  async export(definitionId: number): Promise<Blob> {
    const url = this.buildUrl(`/export?id=${definitionId}`)
    const response = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to export definition')
    return await response.blob()
  }

  /**
   * Import a definition from JSON file
   */
  async import(definitionId: number, file: File): Promise<ImportDefinition> {
    const formData = new FormData()
    formData.append('Filedata', file)
    formData.append('id', String(definitionId))

    const url = this.buildUrl('/import')
    const response = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    })
    if (!response.ok) throw new Error('Failed to import definition')
    const data = await response.json()
    return data.data
  }

  /**
   * Duplicate a definition
   */
  async duplicate(definitionId: number, name: string): Promise<ImportDefinition> {
    const url = this.buildUrl('/duplicate')
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ id: definitionId, name })
    })
    if (!response.ok) throw new Error('Failed to duplicate definition')
    const data = await response.json()
    return data.data
  }
}

/**
 * Export Definition API
 */
export class ExportDefinitionApi extends BaseEntityApi<ExportDefinition> {
  protected buildUrl(route: string): string {
    return `/pimcore-studio/api/data_definitions/export_definitions${route}`
  }

  /**
   * Get export configuration (providers, interpreters, etc.)
   */
  async getConfig(): Promise<DefinitionConfig> {
    const url = this.buildUrl('/get-config')
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to fetch export config')
    return await response.json()
  }

  /**
   * Get columns for a definition
   */
  async getColumns(definitionId: number): Promise<ColumnResponse> {
    const url = this.buildUrl(`/get-columns?id=${definitionId}`)
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to fetch columns')
    return await response.json()
  }

  /**
   * Export a definition as JSON file
   */
  async export(definitionId: number): Promise<Blob> {
    const url = this.buildUrl(`/export?id=${definitionId}`)
    const response = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin'
    })
    if (!response.ok) throw new Error('Failed to export definition')
    return await response.blob()
  }

  /**
   * Duplicate a definition
   */
  async duplicate(definitionId: number, name: string): Promise<ExportDefinition> {
    const url = this.buildUrl('/duplicate')
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ id: definitionId, name })
    })
    if (!response.ok) throw new Error('Failed to duplicate definition')
    const data = await response.json()
    return data.data
  }
}

/**
 * API Instances
 */
export const importDefinitionApi = new ImportDefinitionApi()
export const exportDefinitionApi = new ExportDefinitionApi()
