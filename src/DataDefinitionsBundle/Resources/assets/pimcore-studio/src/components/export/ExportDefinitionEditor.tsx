/**
 * Export Definition Editor Component
 */

import React, { useState, useEffect } from 'react'
import {
  Form,
  Input,
  Select,
  Switch,
  Button,
  Space,
  Tabs,
  Table,
  message,
  Spin,
  Modal
} from 'antd'
import {
  SaveOutlined,
  PlusOutlined,
  DeleteOutlined,
  SettingOutlined,
  DownloadOutlined,
  UploadOutlined,
  CopyOutlined,
  ApiOutlined,
  NodeIndexOutlined
} from '@ant-design/icons'
import type { ExportDefinition, ExportMapping, DefinitionConfig } from '../../types/definitions'
import { exportDefinitionApi } from '../../services/api'
import { MappingConfigDialog } from '../shared/MappingConfigDialog'
import { ProviderConfig } from '../providers'
import { useStyles } from './ExportDefinitionEditor.styles'

interface ExportDefinitionEditorProps {
  definition: ExportDefinition
  onSave: (definition: ExportDefinition) => void
  onCancel: () => void
}

export const ExportDefinitionEditor: React.FC<ExportDefinitionEditorProps> = ({
  definition,
  onSave,
  onCancel
}) => {
  const { styles } = useStyles()
  const [form] = Form.useForm()
  const [config, setConfig] = useState<DefinitionConfig | null>(null)
  const [loading, setLoading] = useState(true)
  const [mappings, setMappings] = useState<ExportMapping[]>(definition.mapping || [])
  const [configDialogVisible, setConfigDialogVisible] = useState(false)
  const [selectedMappingIndex, setSelectedMappingIndex] = useState<number | null>(null)
  const [providerConfig, setProviderConfig] = useState<Record<string, any>>(definition.configuration || {})
  const [selectedProvider, setSelectedProvider] = useState<string | undefined>(definition.provider)
  const [availableClasses, setAvailableClasses] = useState<string[]>([])

  useEffect(() => {
    loadConfig()
    loadAvailableClasses()
  }, [])

  useEffect(() => {
    form.setFieldsValue(definition)
  }, [definition, form])

  const loadConfig = async () => {
    try {
      const configData = await exportDefinitionApi.getConfig()
      setConfig(configData)
    } catch (error) {
      console.error('Failed to load config:', error)
      message.error('Failed to load configuration')
    } finally {
      setLoading(false)
    }
  }

  const loadAvailableClasses = async () => {
    try {
      const response = await fetch('/pimcore-studio/api/class/collection')
      const data = await response.json()
      if (data.items && Array.isArray(data.items)) {
        setAvailableClasses(data.items.map((c: any) => c.name || c.id))
      } else if (Array.isArray(data)) {
        setAvailableClasses(data.map((c: any) => c.name || c.text || c.id))
      }
    } catch (error) {
      console.error('Failed to load classes:', error)
    }
  }

  const handleSave = () => {
    form.validateFields().then(values => {
      onSave({
        ...definition,
        ...values,
        mapping: mappings,
        configuration: providerConfig
      })
    })
  }

  const handleProviderChange = (value: string) => {
    setSelectedProvider(value)
    setProviderConfig({})
  }

  const addMapping = () => {
    setMappings([...mappings, { fromColumn: '', toColumn: '' }])
  }

  const removeMapping = (index: number) => {
    setMappings(mappings.filter((_, i) => i !== index))
  }

  const updateMapping = (index: number, field: keyof ExportMapping, value: any) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], [field]: value }
    setMappings(newMappings)
  }

  const openConfigDialog = (index: number) => {
    setSelectedMappingIndex(index)
    setConfigDialogVisible(true)
  }

  const handleConfigSave = (updatedMapping: ExportMapping) => {
    if (selectedMappingIndex !== null) {
      const newMappings = [...mappings]
      newMappings[selectedMappingIndex] = updatedMapping
      setMappings(newMappings)
    }
    setConfigDialogVisible(false)
    setSelectedMappingIndex(null)
  }

  const handleExportDefinition = async () => {
    if (!definition.id) return
    try {
      const blob = await exportDefinitionApi.export(definition.id)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `export-definition-${definition.name}.json`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (error) {
      message.error('Failed to export definition')
    }
  }

  const handleDuplicate = () => {
    if (!definition.id) return
    const defId = definition.id
    Modal.confirm({
      title: 'Duplicate Definition',
      content: <Input id="duplicate-name" defaultValue={`${definition.name} (Copy)`} />,
      onOk: async () => {
        const nameInput = document.getElementById('duplicate-name') as HTMLInputElement
        try {
          await exportDefinitionApi.duplicate(defId, nameInput?.value || `${definition.name} (Copy)`)
          message.success('Definition duplicated')
        } catch (error) {
          message.error('Failed to duplicate')
        }
      }
    })
  }

  if (loading) {
    return <div className={styles.loadingContainer}><Spin size="large" /></div>
  }

  const mappingColumns = [
    {
      title: 'From Column',
      dataIndex: 'fromColumn',
      key: 'fromColumn',
      render: (_: any, record: ExportMapping, index: number) => (
        <Input
          value={record.fromColumn}
          onChange={e => updateMapping(index, 'fromColumn', e.target.value)}
          placeholder="Source field"
        />
      )
    },
    {
      title: 'To Column',
      dataIndex: 'toColumn',
      key: 'toColumn',
      render: (_: any, record: ExportMapping, index: number) => (
        <Input
          value={record.toColumn}
          onChange={e => updateMapping(index, 'toColumn', e.target.value)}
          placeholder="Target column"
        />
      )
    },
    {
      title: 'Interpreter',
      dataIndex: 'interpreter',
      key: 'interpreter',
      render: (_: any, record: ExportMapping, index: number) => (
        <Select
          className={styles.mappingRowFromColumn}
          value={record.interpreter}
          onChange={value => updateMapping(index, 'interpreter', value)}
          allowClear
          placeholder="Interpreter"
        >
          {config?.interpreter?.map(i => <Select.Option key={i} value={i}>{i}</Select.Option>)}
        </Select>
      )
    },
    {
      title: 'Getter',
      dataIndex: 'getter',
      key: 'getter',
      render: (_: any, record: ExportMapping, index: number) => (
        <Select
          className={styles.mappingRowFromColumn}
          value={record.getter}
          onChange={value => updateMapping(index, 'getter', value)}
          allowClear
          placeholder="Getter"
        >
          {config?.getter?.map(g => <Select.Option key={g} value={g}>{g}</Select.Option>)}
        </Select>
      )
    },
    {
      title: '',
      key: 'actions',
      width: 80,
      render: (_: any, _record: ExportMapping, index: number) => (
        <Space>
          <Button size="small" icon={<SettingOutlined />} onClick={() => openConfigDialog(index)} />
          <Button size="small" danger icon={<DeleteOutlined />} onClick={() => removeMapping(index)} />
        </Space>
      )
    }
  ]

  return (
    <div className={styles.container}>
      <Tabs
        className={styles.tabs}
        items={[
          {
            key: 'settings',
            label: <span><SettingOutlined /> Settings</span>,
            children: (
              <div className={styles.tabContent}>
                <Form
                  form={form}
                  initialValues={definition}
                  labelCol={{ span: 6 }}
                  wrapperCol={{ span: 12 }}
                  className={styles.form}
                >
                  <Form.Item name="name" label="Name" rules={[{ required: true }]}>
                    <Input />
                  </Form.Item>
                  <Form.Item name="provider" label="Provider">
                    <Select allowClear onChange={handleProviderChange}>
                      {config?.providers?.map(p => <Select.Option key={p} value={p}>{p}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="class" label="Class">
                    <Select allowClear showSearch>
                      {availableClasses.map(c => <Select.Option key={c} value={c}>{c}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="filter" label="Filter">
                    <Select allowClear>
                      {config?.filters?.map(f => <Select.Option key={f} value={f}>{f}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="runner" label="Runner">
                    <Select allowClear>
                      {config?.runner?.map(r => <Select.Option key={r} value={r}>{r}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="fetcher" label="Fetcher">
                    <Select allowClear>
                      {config?.fetcher?.map(f => <Select.Option key={f} value={f}>{f}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="enableInheritance" label="Enable Inheritance" valuePropName="checked">
                    <Switch />
                  </Form.Item>
                  <Form.Item name="fetchUnpublished" label="Fetch Unpublished" valuePropName="checked">
                    <Switch />
                  </Form.Item>
                </Form>
              </div>
            )
          },
          {
            key: 'provider',
            label: <span><ApiOutlined /> Provider Settings</span>,
            children: (
              <div className={styles.tabContent}>
                {selectedProvider ? (
                  <ProviderConfig type={selectedProvider} config={providerConfig} onChange={setProviderConfig} />
                ) : (
                  <div className={styles.emptyProvider}>
                    Please select a provider first in the Settings tab.
                  </div>
                )}
              </div>
            )
          },
          {
            key: 'mapping',
            label: <span><NodeIndexOutlined /> Mapping</span>,
            children: (
              <div className={styles.tabContent}>
                <div className={styles.mappingHeader}>
                  <Button type="primary" icon={<PlusOutlined />} onClick={addMapping} size="small">
                    Add Mapping
                  </Button>
                </div>
                <Table
                  dataSource={mappings}
                  columns={mappingColumns}
                  rowKey={(_, index) => index?.toString() || '0'}
                  pagination={false}
                  size="small"
                />
              </div>
            )
          }
        ]}
      />

      <div className={styles.toolbar}>
        <Button icon={<DownloadOutlined />} onClick={handleExportDefinition}>Import Definition</Button>
        <Button icon={<UploadOutlined />} onClick={handleExportDefinition}>Export Definition</Button>
        <Button icon={<CopyOutlined />} onClick={handleDuplicate}>Duplicate</Button>
        <Button type="primary" icon={<SaveOutlined />} onClick={handleSave}>Save</Button>
      </div>

      {config && selectedMappingIndex !== null && (
        <MappingConfigDialog
          visible={configDialogVisible}
          mapping={mappings[selectedMappingIndex]}
          config={config}
          mode="export"
          fromColumnLabel={mappings[selectedMappingIndex].fromColumn}
          toColumnLabel={mappings[selectedMappingIndex].toColumn}
          onSave={handleConfigSave}
          onCancel={() => { setConfigDialogVisible(false); setSelectedMappingIndex(null) }}
        />
      )}
    </div>
  )
}
