/**
 * Import Definition Manager Component
 */

import React from 'react'
import { Modal, Input, message } from 'antd'
import { useTranslation } from 'react-i18next'
import { TabbedEntityManager } from '../shared/TabbedEntityManager'
import { importDefinitionApi } from '../../services/api'
import type { ImportDefinition, DefinitionConfig } from '../../types/definitions'
import { ImportDefinitionDetail } from './ImportDefinitionDetail'

export const ImportDefinitionManager: React.FC = () => {
  const { t } = useTranslation()
  const [config, setConfig] = React.useState<DefinitionConfig | null>(null)

  // Load config on mount
  React.useEffect(() => {
    const loadConfig = async () => {
      try {
        const configData = await importDefinitionApi.getConfig()
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
            const res = await importDefinitionApi.add({ name })
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

  const buildSavePayload = (data: ImportDefinition): Record<string, any> => {
    // Filter out mappings without fromColumn
    const filteredMappings = (data.mapping || []).filter(
      m => m.fromColumn != null && m.fromColumn !== ''
    )
    return {
      ...data,
      mapping: filteredMappings
    }
  }

  return (
    <TabbedEntityManager<ImportDefinition>
      api={importDefinitionApi}
      title={t('data_definitions.menu.import')}
      onAdd={handleAdd}
      buildSavePayload={buildSavePayload}
      getTabTitle={(item) => item.name || `#${item.id}`}
      renderDetail={(data, setData) => {
        if (!config) return null
        return (
          <ImportDefinitionDetail
            definition={data}
            config={config}
            onChange={setData}
          />
        )
      }}
    />
  )
}
