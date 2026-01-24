/**
 * DataDefinitions API Service
 */

import { EntityApi } from '@coreshop/resource/src/entities'
import type { ImportDefinition, ExportDefinition, DefinitionConfig, ColumnResponse } from '../types/definitions'

/**
 * Import Definition API - extends CoreShop EntityApi
 */
export class ImportDefinitionApi extends EntityApi<ImportDefinition> {
  constructor() {
    super({
      basePath: '/pimcore-studio/api',
      resourcePath: '/data_definitions/import_definitions'
    })
  }

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
 * Export Definition API - extends CoreShop EntityApi
 */
export class ExportDefinitionApi extends EntityApi<ExportDefinition> {
  constructor() {
    super({
      basePath: '/pimcore-studio/api',
      resourcePath: '/data_definitions/export_definitions'
    })
  }

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
