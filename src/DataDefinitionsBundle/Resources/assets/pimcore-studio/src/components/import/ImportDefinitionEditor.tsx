/**
 * Import Definition Editor Component
 *
 * Form for editing import definition settings and mappings
 */

import React, { useState, useEffect } from 'react'
import {
  Card,
  Form,
  Input,
  Select,
  Switch,
  Button,
  Space,
  Tabs,
  Table,
  message,
  Spin
} from 'antd'
import { SaveOutlined, CloseOutlined, PlusOutlined, DeleteOutlined } from '@ant-design/icons'
import type { ImportDefinition, ImportMapping, DefinitionConfig } from '../../types/definitions'
import { dataDefinitionsApi } from '../../services/api'

interface ImportDefinitionEditorProps {
  definition: ImportDefinition
  onSave: (definition: ImportDefinition) => void
  onCancel: () => void
}

export const ImportDefinitionEditor: React.FC<ImportDefinitionEditorProps> = ({
  definition,
  onSave,
  onCancel
}) => {
  const [form] = Form.useForm()
  const [config, setConfig] = useState<DefinitionConfig | null>(null)
  const [loading, setLoading] = useState(true)
  const [mappings, setMappings] = useState<ImportMapping[]>(definition.mapping || [])

  useEffect(() => {
    loadConfig()
  }, [])

  useEffect(() => {
    form.setFieldsValue(definition)
  }, [definition, form])

  const loadConfig = async () => {
    try {
      const configData = await dataDefinitionsApi.getImportConfig()
      setConfig(configData)
    } catch (error) {
      console.error('Failed to load config:', error)
      message.error('Failed to load configuration')
    } finally {
      setLoading(false)
    }
  }

  const handleSave = () => {
    form.validateFields().then(values => {
      const updatedDefinition: ImportDefinition = {
        ...definition,
        ...values,
        mapping: mappings
      }
      onSave(updatedDefinition)
    })
  }

  const addMapping = () => {
    setMappings([
      ...mappings,
      {
        fromColumn: '',
        toColumn: '',
        primaryIdentifier: false
      }
    ])
  }

  const removeMapping = (index: number) => {
    setMappings(mappings.filter((_, i) => i !== index))
  }

  const updateMapping = (index: number, field: keyof ImportMapping, value: any) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], [field]: value }
    setMappings(newMappings)
  }

  if (loading) {
    return (
      <div style={{ textAlign: 'center', padding: '50px' }}>
        <Spin size="large" />
      </div>
    )
  }

  const mappingColumns = [
    {
      title: 'From Column',
      dataIndex: 'fromColumn',
      key: 'fromColumn',
      render: (_: any, record: ImportMapping, index: number) => (
        <Input
          value={record.fromColumn}
          onChange={e => updateMapping(index, 'fromColumn', e.target.value)}
          placeholder="Source column"
        />
      )
    },
    {
      title: 'To Column',
      dataIndex: 'toColumn',
      key: 'toColumn',
      render: (_: any, record: ImportMapping, index: number) => (
        <Input
          value={record.toColumn}
          onChange={e => updateMapping(index, 'toColumn', e.target.value)}
          placeholder="Target field"
        />
      )
    },
    {
      title: 'Primary ID',
      dataIndex: 'primaryIdentifier',
      key: 'primaryIdentifier',
      width: 100,
      render: (_: any, record: ImportMapping, index: number) => (
        <Switch
          checked={record.primaryIdentifier}
          onChange={checked => updateMapping(index, 'primaryIdentifier', checked)}
        />
      )
    },
    {
      title: 'Interpreter',
      dataIndex: 'interpreter',
      key: 'interpreter',
      render: (_: any, record: ImportMapping, index: number) => (
        <Select
          style={{ width: '100%' }}
          value={record.interpreter}
          onChange={value => updateMapping(index, 'interpreter', value)}
          allowClear
          placeholder="Select interpreter"
        >
          {config?.interpreters.map(i => (
            <Select.Option key={i} value={i}>{i}</Select.Option>
          ))}
        </Select>
      )
    },
    {
      title: 'Setter',
      dataIndex: 'setter',
      key: 'setter',
      render: (_: any, record: ImportMapping, index: number) => (
        <Select
          style={{ width: '100%' }}
          value={record.setter}
          onChange={value => updateMapping(index, 'setter', value)}
          allowClear
          placeholder="Select setter"
        >
          {config?.setters.map(s => (
            <Select.Option key={s} value={s}>{s}</Select.Option>
          ))}
        </Select>
      )
    },
    {
      title: '',
      key: 'actions',
      width: 50,
      render: (_: any, __: ImportMapping, index: number) => (
        <Button
          type="text"
          danger
          icon={<DeleteOutlined />}
          onClick={() => removeMapping(index)}
        />
      )
    }
  ]

  return (
    <Card
      title={`Edit Import Definition: ${definition.name}`}
      extra={
        <Space>
          <Button icon={<CloseOutlined />} onClick={onCancel}>
            Cancel
          </Button>
          <Button type="primary" icon={<SaveOutlined />} onClick={handleSave}>
            Save
          </Button>
        </Space>
      }
    >
      <Tabs
        defaultActiveKey="general"
        items={[
          {
            key: 'general',
            label: 'General Settings',
            children: (
              <Form
                form={form}
                layout="vertical"
                initialValues={definition}
              >
                <Form.Item
                  name="name"
                  label="Name"
                  rules={[{ required: true, message: 'Please enter a name' }]}
                >
                  <Input />
                </Form.Item>

                <Form.Item name="provider" label="Provider">
                  <Select placeholder="Select provider" allowClear>
                    {config?.providers.map(p => (
                      <Select.Option key={p} value={p}>{p}</Select.Option>
                    ))}
                  </Select>
                </Form.Item>

                <Form.Item name="class" label="Class">
                  <Input placeholder="DataObject class name" />
                </Form.Item>

                <Form.Item name="objectPath" label="Object Path">
                  <Input placeholder="/path/to/objects" />
                </Form.Item>

                <Form.Item name="key" label="Key">
                  <Input placeholder="Object key field" />
                </Form.Item>

                <Form.Item name="loader" label="Loader">
                  <Select placeholder="Select loader" allowClear>
                    {config?.loaders.map(l => (
                      <Select.Option key={l} value={l}>{l}</Select.Option>
                    ))}
                  </Select>
                </Form.Item>

                <Form.Item name="filter" label="Filter">
                  <Select placeholder="Select filter" allowClear>
                    {config?.filters.map(f => (
                      <Select.Option key={f} value={f}>{f}</Select.Option>
                    ))}
                  </Select>
                </Form.Item>

                <Form.Item name="cleaner" label="Cleaner">
                  <Select placeholder="Select cleaner" allowClear>
                    {config?.cleaners.map(c => (
                      <Select.Option key={c} value={c}>{c}</Select.Option>
                    ))}
                  </Select>
                </Form.Item>

                <Form.Item name="runner" label="Runner">
                  <Select placeholder="Select runner" allowClear>
                    {config?.runners.map(r => (
                      <Select.Option key={r} value={r}>{r}</Select.Option>
                    ))}
                  </Select>
                </Form.Item>

                <Form.Item name="persister" label="Persister">
                  <Select placeholder="Select persister" allowClear>
                    {config?.persisters.map(p => (
                      <Select.Option key={p} value={p}>{p}</Select.Option>
                    ))}
                  </Select>
                </Form.Item>
              </Form>
            )
          },
          {
            key: 'options',
            label: 'Options',
            children: (
              <Form form={form} layout="vertical">
                <Form.Item
                  name="renameExistingObjects"
                  label="Rename Existing Objects"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>

                <Form.Item
                  name="relocateExistingObjects"
                  label="Relocate Existing Objects"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>

                <Form.Item
                  name="createVersion"
                  label="Create Version"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>

                <Form.Item
                  name="stopOnException"
                  label="Stop on Exception"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>

                <Form.Item
                  name="skipExistingObjects"
                  label="Skip Existing Objects"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>

                <Form.Item
                  name="skipNewObjects"
                  label="Skip New Objects"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>

                <Form.Item
                  name="omitMandatoryCheck"
                  label="Omit Mandatory Check"
                  valuePropName="checked"
                >
                  <Switch />
                </Form.Item>
              </Form>
            )
          },
          {
            key: 'mapping',
            label: 'Mapping',
            children: (
              <div>
                <div style={{ marginBottom: 16 }}>
                  <Button type="primary" icon={<PlusOutlined />} onClick={addMapping}>
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
    </Card>
  )
}
