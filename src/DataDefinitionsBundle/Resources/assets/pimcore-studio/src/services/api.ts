/**
 * Data Definitions API Service
 */

import type { ImportDefinition, ExportDefinition, DefinitionConfig } from '../types/definitions'

const API_BASE = '/admin/data_definitions'

class ApiError extends Error {
  constructor(message: string, public status?: number) {
    super(message)
    this.name = 'ApiError'
  }
}

async function handleResponse<T>(response: Response): Promise<T> {
  if (!response.ok) {
    throw new ApiError(`API request failed: ${response.statusText}`, response.status)
  }
  const data = await response.json()
  return data
}

class DataDefinitionsApi {
  // Import Definitions
  async getImportDefinitions(): Promise<ImportDefinition[]> {
    const response = await fetch(`${API_BASE}/import_definitions/list`)
    const data = await handleResponse<{ data: ImportDefinition[] }>(response)
    return data.data || []
  }

  async getImportDefinition(id: number): Promise<ImportDefinition> {
    const response = await fetch(`${API_BASE}/import_definitions/get?id=${encodeURIComponent(id)}`)
    const data = await handleResponse<{ data: ImportDefinition }>(response)
    return data.data
  }

  async addImportDefinition(name: string): Promise<ImportDefinition> {
    const response = await fetch(`${API_BASE}/import_definitions/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ name: name.trim() })
    })
    const data = await handleResponse<{ data: ImportDefinition }>(response)
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
    const data = await handleResponse<{ data: ImportDefinition }>(response)
    return data.data
  }

  async deleteImportDefinition(id: number): Promise<void> {
    const response = await fetch(`${API_BASE}/import_definitions/delete?id=${encodeURIComponent(id)}`, {
      method: 'DELETE'
    })
    await handleResponse<void>(response)
  }

  async getImportConfig(): Promise<DefinitionConfig> {
    const response = await fetch(`${API_BASE}/import_definitions/get-config`)
    return await handleResponse<DefinitionConfig>(response)
  }

  async runImportDefinition(id: number, params?: Record<string, string>): Promise<void> {
    const queryParams = new URLSearchParams({ id: id.toString(), ...params })
    const response = await fetch(`${API_BASE}/import_definitions/import?${queryParams}`, {
      method: 'POST'
    })
    await handleResponse<void>(response)
  }

  // Export Definitions
  async getExportDefinitions(): Promise<ExportDefinition[]> {
    const response = await fetch(`${API_BASE}/export_definitions/list`)
    const data = await handleResponse<{ data: ExportDefinition[] }>(response)
    return data.data || []
  }

  async getExportDefinition(id: number): Promise<ExportDefinition> {
    const response = await fetch(`${API_BASE}/export_definitions/get?id=${encodeURIComponent(id)}`)
    const data = await handleResponse<{ data: ExportDefinition }>(response)
    return data.data
  }

  async addExportDefinition(name: string): Promise<ExportDefinition> {
    const response = await fetch(`${API_BASE}/export_definitions/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ name: name.trim() })
    })
    const data = await handleResponse<{ data: ExportDefinition }>(response)
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
    const data = await handleResponse<{ data: ExportDefinition }>(response)
    return data.data
  }

  async deleteExportDefinition(id: number): Promise<void> {
    const response = await fetch(`${API_BASE}/export_definitions/delete?id=${encodeURIComponent(id)}`, {
      method: 'DELETE'
    })
    await handleResponse<void>(response)
  }

  async getExportConfig(): Promise<DefinitionConfig> {
    const response = await fetch(`${API_BASE}/export_definitions/get-config`)
    return await handleResponse<DefinitionConfig>(response)
  }

  async runExportDefinition(id: number, params?: Record<string, string>): Promise<void> {
    const queryParams = new URLSearchParams({ id: id.toString(), ...params })
    const response = await fetch(`${API_BASE}/export_definitions/export?${queryParams}`, {
      method: 'POST'
    })
    await handleResponse<void>(response)
  }
}

export const dataDefinitionsApi = new DataDefinitionsApi()
