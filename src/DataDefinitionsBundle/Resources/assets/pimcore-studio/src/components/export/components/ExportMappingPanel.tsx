/**
 * Export Definition Mapping Panel
 * Grid-based display with groupings like the old ExtJS UI
 */

import React from 'react'
import { Table, Input, Button, Empty, Spin } from 'antd'
import {
  FolderOutlined,
  FolderOpenOutlined,
  FieldBinaryOutlined,
  FontSizeOutlined,
  NumberOutlined,
  CheckSquareOutlined,
  CalendarOutlined,
  LinkOutlined,
  FileImageOutlined,
  EditOutlined,
  DeleteOutlined,
  GlobalOutlined,
  AppstoreOutlined,
  DatabaseOutlined,
  OrderedListOutlined,
  PlusOutlined,
  MinusOutlined
} from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ExportDefinition, ExportMapping, DefinitionConfig, ColumnDefinition } from '../../../types/definitions'
import { exportDefinitionApi } from '../../../services/api'
import { MappingConfigDialog } from '../../shared/MappingConfigDialog'
import { useStyles } from './ExportMappingPanel.styles'

interface ExportMappingPanelProps {
  definition: ExportDefinition
  config: DefinitionConfig
  onChange: (definition: ExportDefinition) => void
}

interface GroupedMapping {
  key: string
  isGroup: boolean
  groupName?: string
  groupPath?: string
  childCount?: number
  mapping?: ExportMapping
  mappingIndex?: number
  fromColumn?: string
  toColumn?: string
  fieldtype?: string
  hasInterpreter?: boolean
  hasGetter?: boolean
}

// Icon mapping for field types
const getFieldIcon = (fieldtype?: string, style?: React.CSSProperties) => {
  const iconStyle = { ...style, marginRight: 6 }
  switch (fieldtype) {
    case 'input':
    case 'textarea':
    case 'wysiwyg':
      return <FontSizeOutlined style={iconStyle} />
    case 'numeric':
    case 'slider':
    case 'quantityValue':
      return <NumberOutlined style={iconStyle} />
    case 'checkbox':
    case 'booleanSelect':
      return <CheckSquareOutlined style={iconStyle} />
    case 'date':
    case 'datetime':
      return <CalendarOutlined style={iconStyle} />
    case 'manyToOneRelation':
    case 'manyToManyRelation':
    case 'manyToManyObjectRelation':
    case 'advancedManyToManyRelation':
    case 'advancedManyToManyObjectRelation':
      return <LinkOutlined style={iconStyle} />
    case 'image':
    case 'hotspotimage':
    case 'imageGallery':
      return <FileImageOutlined style={iconStyle} />
    case 'localizedfields':
      return <GlobalOutlined style={iconStyle} />
    case 'classificationstore':
      return <AppstoreOutlined style={iconStyle} />
    case 'objectbricks':
      return <DatabaseOutlined style={iconStyle} />
    case 'fieldcollections':
      return <OrderedListOutlined style={iconStyle} />
    default:
      return <FieldBinaryOutlined style={iconStyle} />
  }
}

export const ExportMappingPanel: React.FC<ExportMappingPanelProps> = ({
  definition,
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const [classFields, setClassFields] = React.useState<ColumnDefinition[]>([])
  const [loading, setLoading] = React.useState(false)
  const [expandedGroups, setExpandedGroups] = React.useState<Set<string>>(new Set(['fields', 'systemColumn']))
  const [configDialogVisible, setConfigDialogVisible] = React.useState(false)
  const [selectedMappingIndex, setSelectedMappingIndex] = React.useState<number | null>(null)

  // Load columns when definition changes
  React.useEffect(() => {
    if (definition.id && definition.class) {
      setLoading(true)
      exportDefinitionApi.getColumns(definition.id)
        .then(data => {
          setClassFields(data.toColumns || [])
          // Initialize mappings from API response
          if (data.mapping && data.mapping.length > 0) {
            onChange({
              ...definition,
              mapping: data.mapping as ExportMapping[]
            })
          }
        })
        .catch(err => console.error('Failed to load columns:', err))
        .finally(() => setLoading(false))
    }
  }, [definition.id, definition.class])

  const mappings = definition.mapping || []

  // Group mappings by their path/type
  const getGroupKey = (fromColumn: string): string => {
    // Check for localized fields
    if (fromColumn.includes('~')) {
      const parts = fromColumn.split('~')
      return parts[0]
    }
    // Check for classification store
    if (fromColumn.startsWith('classificationstore~')) {
      const match = fromColumn.match(/classificationstore~([^~]+)/)
      if (match) return `classificationstore - ${match[1]}`
    }
    // Check for object bricks
    if (fromColumn.startsWith('objectbrick~')) {
      const match = fromColumn.match(/objectbrick~([^~]+)/)
      if (match) return `objectbrick - ${match[1]}`
    }
    // Check for field collections
    if (fromColumn.startsWith('fieldcollection~')) {
      const match = fromColumn.match(/fieldcollection~([^~]+)/)
      if (match) return `fieldcollection - ${match[1]}`
    }
    // System columns
    const systemFields = ['id', 'key', 'parentId', 'parent', 'type', 'published', 'o_id', 'o_key', 'o_parentId', 'o_parent', 'o_type', 'o_published', 'o_path', 'o_className']
    if (systemFields.includes(fromColumn) || fromColumn.startsWith('o_')) {
      return 'systemColumn'
    }
    return 'fields'
  }

  const getFieldName = (fromColumn: string): string => {
    if (fromColumn.includes('~')) {
      const parts = fromColumn.split('~')
      return parts[parts.length - 1]
    }
    return fromColumn
  }

  // Build grouped data for the table
  const buildGroupedData = (): GroupedMapping[] => {
    const groups: Record<string, { mappings: Array<{ mapping: ExportMapping; index: number }> }> = {}

    mappings.forEach((mapping, index) => {
      const groupKey = getGroupKey(mapping.fromColumn)
      if (!groups[groupKey]) {
        groups[groupKey] = { mappings: [] }
      }
      groups[groupKey].mappings.push({ mapping, index })
    })

    // Define group order
    const groupOrder = ['fields', 'systemColumn']
    const sortedGroups = Object.keys(groups).sort((a, b) => {
      const aIdx = groupOrder.indexOf(a)
      const bIdx = groupOrder.indexOf(b)
      if (aIdx !== -1 && bIdx !== -1) return aIdx - bIdx
      if (aIdx !== -1) return -1
      if (bIdx !== -1) return 1
      return a.localeCompare(b)
    })

    const result: GroupedMapping[] = []

    sortedGroups.forEach(groupKey => {
      const group = groups[groupKey]
      // Add group header
      result.push({
        key: `group-${groupKey}`,
        isGroup: true,
        groupName: groupKey,
        groupPath: groupKey,
        childCount: group.mappings.length
      })

      // Add children if expanded
      if (expandedGroups.has(groupKey)) {
        group.mappings.forEach(({ mapping, index }) => {
          const colDef = classFields.find(c => c.identifier === mapping.fromColumn)
          result.push({
            key: `mapping-${index}`,
            isGroup: false,
            mapping,
            mappingIndex: index,
            fromColumn: mapping.fromColumn,
            toColumn: mapping.toColumn,
            fieldtype: colDef?.fieldtype,
            hasInterpreter: !!mapping.interpreter,
            hasGetter: !!mapping.getter
          })
        })
      }
    })

    return result
  }

  const toggleGroup = (groupKey: string) => {
    setExpandedGroups(prev => {
      const newSet = new Set(prev)
      if (newSet.has(groupKey)) {
        newSet.delete(groupKey)
      } else {
        newSet.add(groupKey)
      }
      return newSet
    })
  }

  const handleToColumnChange = (index: number, value: string) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], toColumn: value }
    onChange({ ...definition, mapping: newMappings })
  }

  const handleOpenConfig = (index: number) => {
    setSelectedMappingIndex(index)
    setConfigDialogVisible(true)
  }

  const handleSaveConfig = (updatedMapping: ExportMapping) => {
    if (selectedMappingIndex !== null) {
      const newMappings = [...mappings]
      newMappings[selectedMappingIndex] = updatedMapping as ExportMapping
      onChange({ ...definition, mapping: newMappings })
    }
    setConfigDialogVisible(false)
    setSelectedMappingIndex(null)
  }

  const handleRemoveMapping = (index: number) => {
    const newMappings = mappings.filter((_, i) => i !== index)
    onChange({ ...definition, mapping: newMappings })
  }

  const columns = [
    {
      title: t('data_definitions.from_column'),
      dataIndex: 'fromColumn',
      key: 'fromColumn',
      width: '40%',
      render: (_: any, record: GroupedMapping) => {
        if (record.isGroup) {
          const isExpanded = expandedGroups.has(record.groupPath!)
          return (
            <div
              className={styles.groupRow}
              onClick={() => toggleGroup(record.groupPath!)}
              style={{ cursor: 'pointer' }}
            >
              {isExpanded ? <MinusOutlined style={{ marginRight: 8 }} /> : <PlusOutlined style={{ marginRight: 8 }} />}
              {isExpanded ? <FolderOpenOutlined style={{ marginRight: 8 }} /> : <FolderOutlined style={{ marginRight: 8 }} />}
              <span className={styles.groupName}>{record.groupName}</span>
              <span className={styles.groupCount}>({record.childCount})</span>
            </div>
          )
        }
        return (
          <div className={styles.fieldRow}>
            <span style={{ paddingLeft: 24 }}>
              {getFieldIcon(record.fieldtype)}
              {getFieldName(record.fromColumn!)}
            </span>
          </div>
        )
      }
    },
    {
      title: t('data_definitions.to_column'),
      dataIndex: 'toColumn',
      key: 'toColumn',
      width: '40%',
      render: (_: any, record: GroupedMapping) => {
        if (record.isGroup) return null
        return (
          <Input
            size="small"
            style={{ width: '100%' }}
            value={record.toColumn || ''}
            onChange={(e) => handleToColumnChange(record.mappingIndex!, e.target.value)}
            placeholder={record.fromColumn}
          />
        )
      }
    },
    {
      title: '',
      key: 'actions',
      width: '20%',
      align: 'right' as const,
      render: (_: any, record: GroupedMapping) => {
        if (record.isGroup) return null
        const hasConfig = record.hasInterpreter || record.hasGetter
        return (
          <div className={styles.actionButtons}>
            <Button
              type="text"
              size="small"
              icon={<EditOutlined style={hasConfig ? { color: '#1890ff' } : undefined} />}
              onClick={() => handleOpenConfig(record.mappingIndex!)}
              title={t('data_definitions.configure')}
            />
            <Button
              type="text"
              size="small"
              danger
              icon={<DeleteOutlined />}
              onClick={() => handleRemoveMapping(record.mappingIndex!)}
              title={t('data_definitions.remove')}
            />
          </div>
        )
      }
    }
  ]

  if (!definition.class) {
    return (
      <div className={styles.container}>
        <Empty
          description={t('data_definitions.select_class_first')}
          image={Empty.PRESENTED_IMAGE_SIMPLE}
        />
      </div>
    )
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loadingContainer}>
          <Spin />
        </div>
      </div>
    )
  }

  const groupedData = buildGroupedData()

  return (
    <div className={styles.container}>
      <Table
        dataSource={groupedData}
        columns={columns}
        pagination={false}
        size="small"
        rowClassName={(record) => record.isGroup ? styles.groupHeaderRow : styles.mappingRow}
        showHeader={true}
        className={styles.mappingTable}
      />

      {config && selectedMappingIndex !== null && (
        <MappingConfigDialog
          visible={configDialogVisible}
          mapping={mappings[selectedMappingIndex]}
          config={config}
          mode="export"
          toColumnLabel={mappings[selectedMappingIndex].fromColumn}
          onSave={handleSaveConfig}
          onCancel={() => { setConfigDialogVisible(false); setSelectedMappingIndex(null) }}
        />
      )}
    </div>
  )
}
