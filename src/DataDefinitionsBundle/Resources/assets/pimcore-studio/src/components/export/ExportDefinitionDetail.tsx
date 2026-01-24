/**
 * Export Definition Detail Component
 * Shows Settings, Provider Settings, and Mapping tabs
 */

import React from 'react'
import { Tabs } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ExportDefinition, DefinitionConfig } from '../../types/definitions'
import { ExportSettingsForm } from './components/ExportSettingsForm'
import { ExportMappingPanel } from './components/ExportMappingPanel'
import { ProviderConfig } from '../providers'
import { useStyles } from './ExportDefinitionDetail.styles'

interface ExportDefinitionDetailProps {
  definition: ExportDefinition
  config: DefinitionConfig
  onChange: (definition: ExportDefinition) => void
}

export const ExportDefinitionDetail: React.FC<ExportDefinitionDetailProps> = ({
  definition,
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()

  const handleProviderConfigChange = (providerConfig: Record<string, any>) => {
    onChange({
      ...definition,
      configuration: providerConfig
    })
  }

  const tabs = [
    {
      key: 'settings',
      label: t('data_definitions.settings'),
      children: (
        <div className={styles.tabContent}>
          <ExportSettingsForm
            definition={definition}
            config={config}
            onChange={onChange}
          />
        </div>
      )
    },
    {
      key: 'provider',
      label: t('data_definitions.provider_settings'),
      children: (
        <div className={styles.tabContent}>
          {definition.provider ? (
            <ProviderConfig
              type={definition.provider}
              config={definition.configuration || {}}
              onChange={handleProviderConfigChange}
            />
          ) : (
            <div className={styles.emptyState}>
              {t('data_definitions.select_provider_first')}
            </div>
          )}
        </div>
      )
    },
    {
      key: 'mapping',
      label: t('data_definitions.mapping'),
      children: (
        <ExportMappingPanel
          definition={definition}
          config={config}
          onChange={onChange}
        />
      )
    }
  ]

  return (
    <div className={styles.root}>
      <Tabs
        defaultActiveKey="settings"
        items={tabs}
        tabBarStyle={{ paddingLeft: 24, paddingRight: 24, marginBottom: 0 }}
        destroyInactiveTabPane={false}
      />
    </div>
  )
}
