/**
 * Export Definition Manager Component
 * Uses CoreShop EntityTabbedManager for consistent UI
 */

import React from 'react'
import { Modal, Input, message } from 'antd'
import { useTranslation } from 'react-i18next'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
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

  return (
    <EntityTabbedManager<ExportDefinition>
      api={exportDefinitionApi}
      getTitle={(listItem, data) => data?.name ?? listItem?.name ?? 'Export Definition'}
      onAdd={handleAdd}
      leftRootTitle={t('data_definitions.menu.export')}
      renderDetail={(data, setData) => {
        if (!data || !config) return null
        return (
          <ExportDefinitionDetail
            definition={data}
            config={config}
            onChange={(updated) => setData(updated)}
          />
        )
      }}
    />
  )
}
