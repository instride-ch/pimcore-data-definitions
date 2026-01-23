/**
 * Export Definition Editor Component
 *
 * Form for editing export definition settings and mappings
 */

import React, { useState, useEffect } from 'react'
import {
  Card,
  Form,
  Input,
  Select,
  Button,
  Space,
  Tabs,
  Table,
  message,
  Spin
} from 'antd'
import { SaveOutlined, CloseOutlined, PlusOutlined, DeleteOutlined } from '@ant-design/icons'
import type { ExportDefinition, ExportMapping, DefinitionConfig } from '../../types/definitions'
import { dataDefinitionsApi } from '../../services/api'

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
  const [form] = Form.useForm()
  const [config, setConfig] = useState<DefinitionConfig | null>(null)
  const [loading, setLoading] = useState(true)
  const [mappings, setMappings] = useState<ExportMapping[]>(definition.mapping || [])

  useEffect(() => {
    loadConfig()
  }, [])

  useEffect(() => {
    form.setFieldsValue(definition)
  }, [definition, form])

  const loadConfig = async () => {
    try {
      const configData = await dataDefinitionsApi.getExportConfig()
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
      const updatedDefinition: ExportDefinition = {
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
        toColumn: ''
      }
    ])
  }

  const removeMapping = (index: number) => {
    setMappings(mappings.filter((_, i) => i !== index))
  }

  const updateMapping = (index: number, field: keyof ExportMapping, value: any) => {
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
      title: 'Getter',
      dataIndex: 'getter',
      key: 'getter',
      render: (_: any, record: ExportMapping, index: number) => (
        <Select
          style={{ width: '100%' }}
          value={record.getter}
          onChange={value => updateMapping(index, 'getter', value)}
          allowClear
          placeholder="Select getter"
        >
          {config?.getters.map(g => (
            <Select.Option key={g} value={g}>{g}</Select.Option>
          ))}
        </Select>
      )
    },
    {
      title: '',
      key: 'actions',
      width: 50,
      render: (_: any, __: ExportMapping, index: number) => (
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
      title={`Edit Export Definition: ${definition.name}`}
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

                <Form.Item name="filter" label="Filter">
                  <Select placeholder="Select filter" allowClear>
                    {config?.filters.map(f => (
                      <Select.Option key={f} value={f}>{f}</Select.Option>
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
