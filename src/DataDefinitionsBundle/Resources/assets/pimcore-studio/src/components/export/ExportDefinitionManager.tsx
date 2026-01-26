/**
 * Export Definition Manager Component
 */

import React from 'react'
import { Modal, Input, message } from 'antd'
import { useTranslation } from 'react-i18next'
import { TabbedEntityManager } from '../shared/TabbedEntityManager'
import { exportDefinitionApi } from '../../services/api'
import type { ExportDefinition, DefinitionConfig } from '../../types/definitions'
import { ExportDefinitionDetail } from './ExportDefinitionDetail'

export const ExportDefinitionManager: React.FC = () => {
  const { t } = useTranslation()
  const [config, setConfig] = React.useState<DefinitionConfig | null>(null)

  // Load config on mount
  React.useEffect(() => {
    const loadConfig = async () => {
      try {
        const configData = await exportDefinitionApi.getConfig()
        setConfig(configData)
      } catch (error) {
        console.error('Failed to load config:', error)
      }
    }
    void loadConfig()
  }, [])

  const handleAdd = async (): Promise<number> => {
    return new Promise((resolve, reject) => {
      let inputValue = ''
      Modal.confirm({
        title: t('data_definitions.add'),
        content: (
          <Input
            placeholder={t('data_definitions.name')}
            onChange={e => { inputValue = e.target.value }}
            autoFocus
          />
        ),
        onOk: async () => {
          const name = inputValue.trim()
          if (!name) {
            message.warning(t('data_definitions.name') + ' required')
            reject(new Error('Name required'))
            return
          }
          try {
            const res = await exportDefinitionApi.add({ name })
            resolve(res.id!)
          } catch (error) {
            message.error('Failed to create definition')
            reject(error)
          }
        },
        onCancel: () => reject(new Error('Cancelled'))
      })
    })
  }

  const buildSavePayload = (data: ExportDefinition): Record<string, any> => {
    // Filter out mappings without toColumn
    const filteredMappings = (data.mapping || []).filter(
      m => m.toColumn != null && m.toColumn !== ''
    )
    return {
      ...data,
      mapping: filteredMappings
    }
  }

  return (
    <TabbedEntityManager<ExportDefinition>
      api={exportDefinitionApi}
      title={t('data_definitions.menu.export')}
      onAdd={handleAdd}
      buildSavePayload={buildSavePayload}
      getTabTitle={(item) => item.name || `#${item.id}`}
      renderDetail={(data, setData) => {
        if (!config) return null
        return (
          <ExportDefinitionDetail
            definition={data}
            config={config}
            onChange={setData}
          />
        )
      }}
    />
  )
}
