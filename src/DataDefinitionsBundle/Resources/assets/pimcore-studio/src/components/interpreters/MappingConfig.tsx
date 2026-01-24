/**
 * Mapping Interpreter Configuration
 */

import React from 'react'
import { Form, Switch, Table, Input, Button, Space } from 'antd'
import { PlusOutlined, DeleteOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

interface MappingRow {
  from: string
  to: string
}

export const MappingConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const mappings: MappingRow[] = config.mapping || []
  const returnNullWhenNotFound = config.return_null_when_not_found !== false

  const updateMappings = (newMappings: MappingRow[]) => {
    onChange({ ...config, mapping: newMappings })
  }

  const addRow = () => {
    updateMappings([...mappings, { from: '', to: '' }])
  }

  const removeRow = (index: number) => {
    updateMappings(mappings.filter((_, i) => i !== index))
  }

  const updateRow = (index: number, field: 'from' | 'to', value: string) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], [field]: value }
    updateMappings(newMappings)
  }

  const columns = [
    {
      title: t('data_definitions.from_column'),
      dataIndex: 'from',
      key: 'from',
      render: (_: any, record: MappingRow, index: number) => (
        <Input
          value={record.from}
          onChange={e => updateRow(index, 'from', e.target.value)}
        />
      )
    },
    {
      title: t('data_definitions.to_column'),
      dataIndex: 'to',
      key: 'to',
      render: (_: any, record: MappingRow, index: number) => (
        <Input
          value={record.to}
          onChange={e => updateRow(index, 'to', e.target.value)}
        />
      )
    },
    {
      title: '',
      key: 'actions',
      width: 50,
      render: (_: any, __: MappingRow, index: number) => (
        <Button
          type="text"
          danger
          icon={<DeleteOutlined />}
          onClick={() => removeRow(index)}
        />
      )
    }
  ]

  return (
    <div>
      <Form.Item label={t('data_definitions.interpreter.mapping.return_null')}>
        <Switch
          checked={returnNullWhenNotFound}
          onChange={checked => onChange({ ...config, return_null_when_not_found: checked })}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.interpreter.mapping.mapping')}>
        <Space direction="vertical" style={{ width: '100%' }}>
          <Button type="dashed" onClick={addRow} icon={<PlusOutlined />}>
            {t('data_definitions.add')}
          </Button>
          <Table
            dataSource={mappings}
            columns={columns}
            rowKey={(_, index) => index?.toString() || '0'}
            pagination={false}
            size="small"
          />
        </Space>
      </Form.Item>
    </div>
  )
}
