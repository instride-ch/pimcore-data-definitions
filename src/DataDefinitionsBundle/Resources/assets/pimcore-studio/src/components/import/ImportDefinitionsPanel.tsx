/**
 * Import Definitions Panel Component
 *
 * Main panel for managing import definitions in Pimcore Studio
 */

import React, { useEffect, useState, useCallback } from 'react'
import { Table, Button, Space, Modal, Input, message, Spin, Empty, Card, Popconfirm, Tooltip } from 'antd'
import { PlusOutlined, DeleteOutlined, PlayCircleOutlined, EditOutlined, ReloadOutlined } from '@ant-design/icons'
import type { ImportDefinition } from '../../types/definitions'
import { dataDefinitionsApi } from '../../services/api'
import { ImportDefinitionEditor } from './ImportDefinitionEditor'

interface ImportDefinitionsPanelProps {
  className?: string
}

export const ImportDefinitionsPanel: React.FC<ImportDefinitionsPanelProps> = ({ className }) => {
  const [definitions, setDefinitions] = useState<ImportDefinition[]>([])
  const [loading, setLoading] = useState(true)
  const [isAddModalVisible, setIsAddModalVisible] = useState(false)
  const [newDefinitionName, setNewDefinitionName] = useState('')
  const [selectedDefinition, setSelectedDefinition] = useState<ImportDefinition | null>(null)
  const [isEditing, setIsEditing] = useState(false)

  const loadDefinitions = useCallback(async () => {
    setLoading(true)
    try {
      const data = await dataDefinitionsApi.getImportDefinitions()
      setDefinitions(data)
    } catch (error) {
      console.error('Failed to load import definitions:', error)
      message.error('Failed to load import definitions')
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadDefinitions()
  }, [loadDefinitions])

  const handleAdd = async () => {
    if (!newDefinitionName.trim()) {
      message.warning('Please enter a name for the definition')
      return
    }

    try {
      const newDef = await dataDefinitionsApi.addImportDefinition(newDefinitionName)
      setDefinitions([...definitions, newDef])
      setIsAddModalVisible(false)
      setNewDefinitionName('')
      message.success('Import definition created successfully')
    } catch (error) {
      console.error('Failed to create import definition:', error)
      message.error('Failed to create import definition')
    }
  }

  const handleDelete = async (id: number) => {
    try {
      await dataDefinitionsApi.deleteImportDefinition(id)
      setDefinitions(definitions.filter(d => d.id !== id))
      message.success('Import definition deleted successfully')
    } catch (error) {
      console.error('Failed to delete import definition:', error)
      message.error('Failed to delete import definition')
    }
  }

  const handleRun = async (id: number) => {
    try {
      await dataDefinitionsApi.runImportDefinition(id)
      message.success('Import started successfully')
    } catch (error) {
      console.error('Failed to run import:', error)
      message.error('Failed to run import')
    }
  }

  const handleEdit = async (definition: ImportDefinition) => {
    try {
      const fullDef = await dataDefinitionsApi.getImportDefinition(definition.id)
      setSelectedDefinition(fullDef)
      setIsEditing(true)
    } catch (error) {
      console.error('Failed to load definition details:', error)
      message.error('Failed to load definition details')
    }
  }

  const handleSave = async (definition: ImportDefinition) => {
    try {
      await dataDefinitionsApi.saveImportDefinition(definition)
      message.success('Import definition saved successfully')
      setIsEditing(false)
      setSelectedDefinition(null)
      loadDefinitions()
    } catch (error) {
      console.error('Failed to save import definition:', error)
      message.error('Failed to save import definition')
    }
  }

  const handleCancelEdit = () => {
    setIsEditing(false)
    setSelectedDefinition(null)
  }

  const columns = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 80
    },
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name'
    },
    {
      title: 'Provider',
      dataIndex: 'provider',
      key: 'provider'
    },
    {
      title: 'Class',
      dataIndex: 'class',
      key: 'class'
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 150,
      render: (_: any, record: ImportDefinition) => (
        <Space size="small">
          <Tooltip title="Edit">
            <Button
              type="text"
              size="small"
              icon={<EditOutlined />}
              onClick={() => handleEdit(record)}
            />
          </Tooltip>
          <Tooltip title="Run Import">
            <Button
              type="text"
              size="small"
              icon={<PlayCircleOutlined />}
              onClick={() => handleRun(record.id)}
            />
          </Tooltip>
          <Popconfirm
            title="Delete this definition?"
            description="Are you sure you want to delete this import definition?"
            onConfirm={() => handleDelete(record.id)}
            okText="Yes"
            cancelText="No"
          >
            <Tooltip title="Delete">
              <Button
                type="text"
                size="small"
                danger
                icon={<DeleteOutlined />}
              />
            </Tooltip>
          </Popconfirm>
        </Space>
      )
    }
  ]

  if (isEditing && selectedDefinition) {
    return (
      <ImportDefinitionEditor
        definition={selectedDefinition}
        onSave={handleSave}
        onCancel={handleCancelEdit}
      />
    )
  }

  return (
    <Card
      className={className}
      title="Import Definitions"
      extra={
        <Space>
          <Button
            icon={<ReloadOutlined />}
            onClick={loadDefinitions}
          >
            Reload
          </Button>
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={() => setIsAddModalVisible(true)}
          >
            Add Definition
          </Button>
        </Space>
      }
    >
      {loading ? (
        <div style={{ textAlign: 'center', padding: '50px' }}>
          <Spin size="large" />
        </div>
      ) : definitions.length === 0 ? (
        <Empty
          description="No import definitions found"
          image={Empty.PRESENTED_IMAGE_SIMPLE}
        >
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={() => setIsAddModalVisible(true)}
          >
            Create First Definition
          </Button>
        </Empty>
      ) : (
        <Table
          dataSource={definitions}
          columns={columns}
          rowKey="id"
          pagination={{ pageSize: 20 }}
          size="small"
        />
      )}

      <Modal
        title="Add Import Definition"
        open={isAddModalVisible}
        onOk={handleAdd}
        onCancel={() => {
          setIsAddModalVisible(false)
          setNewDefinitionName('')
        }}
        okText="Create"
      >
        <Input
          placeholder="Definition Name"
          value={newDefinitionName}
          onChange={e => setNewDefinitionName(e.target.value)}
          onPressEnter={handleAdd}
          autoFocus
        />
      </Modal>
    </Card>
  )
}
