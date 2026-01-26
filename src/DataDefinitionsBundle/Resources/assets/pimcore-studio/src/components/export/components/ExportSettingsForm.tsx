/**
 * Export Definition Settings Form
 */

import React from 'react'
import { Form, Input, Select, Checkbox } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ExportDefinition, DefinitionConfig } from '../../../types/definitions'

interface ExportSettingsFormProps {
  definition: ExportDefinition
  config: DefinitionConfig
  onChange: (definition: ExportDefinition) => void
}

// Module-level cache for classes
let cachedClasses: string[] | null = null
let loadPromise: Promise<string[]> | null = null

const loadClasses = async (): Promise<string[]> => {
  if (cachedClasses) return cachedClasses
  if (loadPromise) return loadPromise

  loadPromise = (async (): Promise<string[]> => {
    try {
      const response = await fetch('/pimcore-studio/api/class/collection')
      const data = await response.json()
      let result: string[] = []
      if (data.items && Array.isArray(data.items)) {
        result = data.items.map((c: any) => c.name || c.id)
      } else if (Array.isArray(data)) {
        result = data.map((c: any) => c.name || c.text || c.id)
      }
      cachedClasses = result
      return result
    } catch (err) {
      console.error('Failed to load classes:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const ExportSettingsForm: React.FC<ExportSettingsFormProps> = ({
  definition,
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [availableClasses, setAvailableClasses] = React.useState<string[]>(cachedClasses || [])

  React.useEffect(() => {
    void (async () => {
      const classes = await loadClasses()
      setAvailableClasses(classes)
    })()
  }, [])

  const handleFieldChange = (field: keyof ExportDefinition, value: any) => {
    onChange({ ...definition, [field]: value })
  }

  return (
    <Form
      layout="horizontal"
      labelCol={{ span: 6 }}
      wrapperCol={{ span: 14 }}
    >
      <Form.Item label={t('data_definitions.name')} required>
        <Input
          value={definition.name}
          onChange={e => handleFieldChange('name', e.target.value)}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.provider')}>
        <Select
          value={definition.provider}
          onChange={v => handleFieldChange('provider', v)}
          allowClear
        >
          {config.providers?.map(p => (
            <Select.Option key={p} value={p}>{p}</Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label={t('data_definitions.class')} required>
        <Select
          value={definition.class}
          onChange={v => handleFieldChange('class', v)}
          allowClear
          showSearch
        >
          {availableClasses.map(c => (
            <Select.Option key={c} value={c}>{c}</Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label={t('data_definitions.fetcher')}>
        <Select
          value={definition.fetcher}
          onChange={v => handleFieldChange('fetcher', v)}
          allowClear
        >
          {config.fetcher?.map(f => (
            <Select.Option key={f} value={f}>{f}</Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label={t('data_definitions.filter')}>
        <Select
          value={definition.filter}
          onChange={v => handleFieldChange('filter', v)}
          allowClear
        >
          {config.filters?.map(f => (
            <Select.Option key={f} value={f}>{f}</Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label={t('data_definitions.runner')}>
        <Select
          value={definition.runner}
          onChange={v => handleFieldChange('runner', v)}
          allowClear
        >
          {config.runner?.map(r => (
            <Select.Option key={r} value={r}>{r}</Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label={t('data_definitions.enable_inheritance')}>
        <Checkbox
          checked={definition.enableInheritance}
          onChange={e => handleFieldChange('enableInheritance', e.target.checked)}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.fetch_unpublished')}>
        <Checkbox
          checked={definition.fetchUnpublished}
          onChange={e => handleFieldChange('fetchUnpublished', e.target.checked)}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.stop_on_exception')}>
        <Checkbox
          checked={definition.stopOnException}
          onChange={e => handleFieldChange('stopOnException', e.target.checked)}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.failure_notification_document')}>
        <Input
          value={definition.failureNotificationDocument}
          onChange={e => handleFieldChange('failureNotificationDocument', e.target.value)}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.success_notification_document')}>
        <Input
          value={definition.successNotificationDocument}
          onChange={e => handleFieldChange('successNotificationDocument', e.target.value)}
        />
      </Form.Item>
    </Form>
  )
}
