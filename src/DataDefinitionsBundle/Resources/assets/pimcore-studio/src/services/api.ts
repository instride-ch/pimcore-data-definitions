/**
 * Data Definitions API Service
 */

import type { ImportDefinition, ExportDefinition, DefinitionConfig } from '../types/definitions'

const API_BASE = '/admin/data_definitions'

class DataDefinitionsApi {
  // Import Definitions
  async getImportDefinitions(): Promise<ImportDefinition[]> {
    const response = await fetch(`${API_BASE}/import_definitions/list`)
    const data = await response.json()
    return data.data || []
  }

  async getImportDefinition(id: number): Promise<ImportDefinition> {
    const response = await fetch(`${API_BASE}/import_definitions/get?id=${id}`)
    const data = await response.json()
    return data.data
  }

  async addImportDefinition(name: string): Promise<ImportDefinition> {
    const response = await fetch(`${API_BASE}/import_definitions/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ name })
    })
    const data = await response.json()
    return data.data
  }

  async saveImportDefinition(definition: ImportDefinition): Promise<ImportDefinition> {
    const response = await fetch(`${API_BASE}/import_definitions/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(definition)
    })
    const data = await response.json()
    return data.data
  }

  async deleteImportDefinition(id: number): Promise<void> {
    await fetch(`${API_BASE}/import_definitions/delete?id=${id}`, {
      method: 'DELETE'
    })
  }

  async getImportConfig(): Promise<DefinitionConfig> {
    const response = await fetch(`${API_BASE}/import_definitions/get-config`)
    return await response.json()
  }

  async runImportDefinition(id: number, params?: Record<string, string>): Promise<void> {
    const queryParams = new URLSearchParams({ id: id.toString(), ...params })
    await fetch(`${API_BASE}/import_definitions/import?${queryParams}`, {
      method: 'POST'
    })
  }

  // Export Definitions
  async getExportDefinitions(): Promise<ExportDefinition[]> {
    const response = await fetch(`${API_BASE}/export_definitions/list`)
    const data = await response.json()
    return data.data || []
  }

  async getExportDefinition(id: number): Promise<ExportDefinition> {
    const response = await fetch(`${API_BASE}/export_definitions/get?id=${id}`)
    const data = await response.json()
    return data.data
  }

  async addExportDefinition(name: string): Promise<ExportDefinition> {
    const response = await fetch(`${API_BASE}/export_definitions/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ name })
    })
    const data = await response.json()
    return data.data
  }

  async saveExportDefinition(definition: ExportDefinition): Promise<ExportDefinition> {
    const response = await fetch(`${API_BASE}/export_definitions/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(definition)
    })
    const data = await response.json()
    return data.data
  }

  async deleteExportDefinition(id: number): Promise<void> {
    await fetch(`${API_BASE}/export_definitions/delete?id=${id}`, {
      method: 'DELETE'
    })
  }

  async getExportConfig(): Promise<DefinitionConfig> {
    const response = await fetch(`${API_BASE}/export_definitions/get-config`)
    return await response.json()
  }

  async runExportDefinition(id: number, params?: Record<string, string>): Promise<void> {
    const queryParams = new URLSearchParams({ id: id.toString(), ...params })
    await fetch(`${API_BASE}/export_definitions/export?${queryParams}`, {
      method: 'POST'
    })
  }
}

export const dataDefinitionsApi = new DataDefinitionsApi()
