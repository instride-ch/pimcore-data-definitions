/**
 * Import Definition Editor Component
 */

import React, { useState, useEffect } from 'react'
import { Form, Input, Select, Checkbox, Button, Tabs, Tree, message, Spin, Modal, Space, Typography } from 'antd'
import type { DataNode } from 'antd/es/tree'
import {
  SaveOutlined,
  DownloadOutlined,
  UploadOutlined,
  CopyOutlined,
  EditOutlined,
  DeleteOutlined,
  FolderOutlined,
  FieldBinaryOutlined,
  SettingOutlined,
  ApiOutlined,
  NodeIndexOutlined
} from '@ant-design/icons'
import type { ImportDefinition, ImportMapping, DefinitionConfig } from '../../types/definitions'
import { importDefinitionApi } from '../../services/api'
import { MappingConfigDialog } from '../shared/MappingConfigDialog'
import { ProviderConfig } from '../providers'
import { useStyles } from './ImportDefinitionEditor.styles'

const { Title } = Typography

interface ImportDefinitionEditorProps {
  definition: ImportDefinition
  onSave: (definition: ImportDefinition) => void
  onCancel: () => void
}

interface GroupedMapping {
  group: string
  mappings: Array<ImportMapping & { originalIndex: number }>
}

export const ImportDefinitionEditor: React.FC<ImportDefinitionEditorProps> = ({
  definition,
  onSave,
  onCancel
}) => {
  const { styles } = useStyles()
  const [form] = Form.useForm()
  const [config, setConfig] = useState<DefinitionConfig | null>(null)
  const [loading, setLoading] = useState(true)
  const [mappings, setMappings] = useState<ImportMapping[]>(definition.mapping || [])
  const [configDialogVisible, setConfigDialogVisible] = useState(false)
  const [selectedMappingIndex, setSelectedMappingIndex] = useState<number | null>(null)
  const [providerConfig, setProviderConfig] = useState<Record<string, any>>(definition.configuration || {})
  const [selectedProvider, setSelectedProvider] = useState<string | undefined>(definition.provider)
  const [fromColumns, setFromColumns] = useState<Array<{ id: string; identifier: string; label?: string }>>([])
  const [toColumns, setToColumns] = useState<Array<{ identifier: string; label?: string; group?: string }>>([])
  const [availableClasses, setAvailableClasses] = useState<string[]>([])
  const [expandedKeys, setExpandedKeys] = useState<string[]>(['fields', 'systemColumn'])

  useEffect(() => {
    loadConfig()
    loadAvailableClasses()
    if (definition.id && definition.class) {
      loadColumns()
    }
  }, [])

  useEffect(() => {
    form.setFieldsValue(definition)
  }, [definition, form])

  const loadConfig = async () => {
    try {
      const configData = await importDefinitionApi.getConfig()
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

  const loadColumns = async () => {
    try {
      const columns = await importDefinitionApi.getColumns(definition.id)
      setFromColumns(columns.fromColumns)
      setToColumns(columns.toColumns)
      if (mappings.length === 0 && columns.mapping.length > 0) {
        setMappings(columns.mapping)
      }
    } catch (error) {
      console.error('Failed to load columns:', error)
    }
  }

  const handleSave = () => {
    form.validateFields().then(values => {
      // Only save mappings that have a fromColumn configured (not null, undefined, or empty string)
      const filteredMappings = mappings.filter(m => m.fromColumn != null && m.fromColumn !== '')
      onSave({
        ...definition,
        ...values,
        mapping: filteredMappings,
        configuration: providerConfig
      })
    })
  }

  const handleProviderChange = (value: string) => {
    setSelectedProvider(value)
    setProviderConfig({})
  }

  const updateMapping = (index: number, field: keyof ImportMapping, value: any) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], [field]: value }
    setMappings(newMappings)
  }

  const removeMapping = (index: number) => {
    setMappings(mappings.filter((_, i) => i !== index))
  }

  const openConfigDialog = (index: number) => {
    setSelectedMappingIndex(index)
    setConfigDialogVisible(true)
  }

  const handleConfigSave = (updatedMapping: ImportMapping) => {
    if (selectedMappingIndex !== null) {
      const newMappings = [...mappings]
      newMappings[selectedMappingIndex] = updatedMapping
      setMappings(newMappings)
    }
    setConfigDialogVisible(false)
    setSelectedMappingIndex(null)
  }

  const handleExportDefinition = async () => {
    try {
      const blob = await importDefinitionApi.export(definition.id)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `import-definition-${definition.name}.json`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (error) {
      message.error('Failed to export definition')
    }
  }

  const handleDuplicate = () => {
    Modal.confirm({
      title: 'Duplicate Definition',
      content: <Input id="duplicate-name" defaultValue={`${definition.name} (Copy)`} />,
      onOk: async () => {
        const nameInput = document.getElementById('duplicate-name') as HTMLInputElement
        try {
          await importDefinitionApi.duplicate(definition.id, nameInput?.value || `${definition.name} (Copy)`)
          message.success('Definition duplicated')
        } catch (error) {
          message.error('Failed to duplicate')
        }
      }
    })
  }

  const getGroupedMappings = (): GroupedMapping[] => {
    const groups: Record<string, Array<ImportMapping & { originalIndex: number }>> = {
      fields: [],
      systemColumn: []
    }

    mappings.forEach((mapping, index) => {
      const isSystem = mapping.toColumn?.startsWith('o_') ||
        ['id', 'key', 'parentId', 'parent', 'type', 'published'].includes(mapping.toColumn || '')
      const group = isSystem ? 'systemColumn' : 'fields'
      groups[group].push({ ...mapping, originalIndex: index })
    })

    return Object.entries(groups)
      .filter(([_, items]) => items.length > 0)
      .map(([group, items]) => ({ group, mappings: items }))
  }

  const buildTreeData = (): DataNode[] => {
    return getGroupedMappings().map(group => ({
      key: group.group,
      title: <span className={styles.groupTitle}>{group.group}</span>,
      icon: <FolderOutlined />,
      children: group.mappings.map(mapping => ({
        key: `mapping-${mapping.originalIndex}`,
        icon: <FieldBinaryOutlined />,
        title: (
          <div className={styles.mappingRow}>
            <span className={styles.mappingRowToColumn}>{mapping.toColumn}</span>
            <Select
              size="small"
              className={styles.mappingRowFromColumn}
              value={mapping.fromColumn || undefined}
              onChange={value => updateMapping(mapping.originalIndex, 'fromColumn', value)}
              allowClear
              placeholder="From column"
            >
              {fromColumns.map(c => (
                <Select.Option key={c.identifier || c.id} value={c.identifier || c.id}>
                  {c.label || c.identifier || c.id}
                </Select.Option>
              ))}
            </Select>
            <Checkbox
              checked={mapping.primaryIdentifier}
              onChange={e => updateMapping(mapping.originalIndex, 'primaryIdentifier', e.target.checked)}
            >
              Primary
            </Checkbox>
            <Button size="small" icon={<EditOutlined />} onClick={() => openConfigDialog(mapping.originalIndex)} />
            <Button size="small" danger icon={<DeleteOutlined />} onClick={() => removeMapping(mapping.originalIndex)} />
          </div>
        )
      }))
    }))
  }

  if (loading) {
    return <div className={styles.loadingContainer}><Spin size="large" /></div>
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <Title level={5} className={styles.headerTitle}>
          {definition.name} (ID: {definition.id})
        </Title>
        <Space>
          <Button size="small" icon={<DownloadOutlined />} onClick={handleExportDefinition}>
            Export
          </Button>
          <Button size="small" icon={<CopyOutlined />} onClick={handleDuplicate}>
            Duplicate
          </Button>
        </Space>
      </div>

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
                  wrapperCol={{ span: 14 }}
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
                  <Form.Item name="loader" label="Loader">
                    <Select allowClear>
                      {config?.loaders?.map(l => <Select.Option key={l} value={l}>{l}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="class" label="Class">
                    <Select allowClear showSearch>
                      {availableClasses.map(c => <Select.Option key={c} value={c}>{c}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="objectPath" label="Path">
                    <Input />
                  </Form.Item>
                  <Form.Item name="key" label="Key">
                    <Input />
                  </Form.Item>
                  <Form.Item name="cleaner" label="Cleaner">
                    <Select allowClear>
                      {config?.cleaner?.map(c => <Select.Option key={c} value={c}>{c}</Select.Option>)}
                    </Select>
                  </Form.Item>
                  <Form.Item name="persister" label="Persister">
                    <Select allowClear>
                      {config?.persister?.map(p => <Select.Option key={p} value={p}>{p}</Select.Option>)}
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
                  <Form.Item name="relocateExistingObjects" label="Relocate existing Objects" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="renameExistingObjects" label="Rename existing Objects" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="skipExistingObjects" label="Skip Existing Objects" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="skipNewObjects" label="Skip New Objects" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="createVersion" label="Create new Version" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="stopOnException" label="Stop on Exception" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="omitMandatoryCheck" label="Omit mandatory check" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="forceLoadObject" label="Force Load Object" valuePropName="checked">
                    <Checkbox />
                  </Form.Item>
                  <Form.Item name="failureNotificationDocument" label="Failure Notification">
                    <Input placeholder="Document path" />
                  </Form.Item>
                  <Form.Item name="successNotificationDocument" label="Success Notification">
                    <Input placeholder="Document path" />
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
                  <span className={styles.mappingHeaderCol}>To Column</span>
                  <span className={styles.mappingHeaderColFrom}>From Column</span>
                  <span className={styles.mappingHeaderColPrimary}>Primary</span>
                </div>
                <Tree
                  showIcon
                  defaultExpandAll
                  expandedKeys={expandedKeys}
                  onExpand={keys => setExpandedKeys(keys as string[])}
                  treeData={buildTreeData()}
                />
              </div>
            )
          }
        ]}
      />

      <div className={styles.toolbar}>
        <Button type="primary" icon={<SaveOutlined />} onClick={handleSave}>
          Save
        </Button>
      </div>

      {config && selectedMappingIndex !== null && (
        <MappingConfigDialog
          visible={configDialogVisible}
          mapping={mappings[selectedMappingIndex]}
          config={config}
          mode="import"
          fromColumnLabel={mappings[selectedMappingIndex].fromColumn}
          toColumnLabel={mappings[selectedMappingIndex].toColumn}
          onSave={handleConfigSave}
          onCancel={() => { setConfigDialogVisible(false); setSelectedMappingIndex(null) }}
        />
      )}
    </div>
  )
}
