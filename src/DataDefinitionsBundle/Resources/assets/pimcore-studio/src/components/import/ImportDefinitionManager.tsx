/**
 * Import Definition Manager Component
 * Uses CoreShop EntityTabbedManager for consistent UI
 */

import React from 'react'
import { Modal, Input, message } from 'antd'
import { useTranslation } from 'react-i18next'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
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
            resolve(res.data.id!)
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
    <EntityTabbedManager<ImportDefinition>
      api={importDefinitionApi}
      getTitle={(listItem, data) => data?.name ?? listItem?.name ?? 'Import Definition'}
      buildSavePayload={buildSavePayload}
      onAdd={handleAdd}
      leftRootTitle={t('data_definitions.menu.import')}
      renderDetail={(data, setData) => {
        if (!data || !config) return null
        return (
          <ImportDefinitionDetail
            definition={data}
            config={config}
            onChange={(updated) => setData(updated)}
          />
        )
      }}
    />
  )
}
