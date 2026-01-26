/**
 * Import Definition Mapping Panel
 * Grid-based display with groupings like the old ExtJS UI
 */

import React from 'react'
import { Table, Select, Checkbox, Button, Empty, Spin } from 'antd'
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
import type { ImportDefinition, ImportMapping, DefinitionConfig, ColumnDefinition } from '../../../types/definitions'
import { importDefinitionApi } from '../../../services/api'
import { MappingConfigDialog } from '../../shared/MappingConfigDialog'
import { useStyles } from './MappingPanel.styles'

interface MappingPanelProps {
  definition: ImportDefinition
  config: DefinitionConfig
  onChange: (definition: ImportDefinition) => void
}

interface GroupedMapping {
  key: string
  isGroup: boolean
  groupName?: string
  groupPath?: string
  childCount?: number
  mapping?: ImportMapping
  mappingIndex?: number
  toColumn?: string
  fromColumn?: string
  primaryIdentifier?: boolean
  fieldtype?: string
  hasInterpreter?: boolean
  hasSetter?: boolean
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

export const MappingPanel: React.FC<MappingPanelProps> = ({
  definition,
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const [toColumns, setToColumns] = React.useState<ColumnDefinition[]>([])
  const [fromColumns, setFromColumns] = React.useState<ColumnDefinition[]>([])
  const [loading, setLoading] = React.useState(false)
  const [expandedGroups, setExpandedGroups] = React.useState<Set<string>>(new Set(['fields', 'systemColumn']))
  const [configDialogVisible, setConfigDialogVisible] = React.useState(false)
  const [selectedMappingIndex, setSelectedMappingIndex] = React.useState<number | null>(null)

  // Load columns when definition changes
  React.useEffect(() => {
    if (definition.id && definition.class) {
      setLoading(true)
      importDefinitionApi.getColumns(definition.id)
        .then(data => {
          setFromColumns(data.fromColumns || [])
          setToColumns(data.toColumns || [])
          // Initialize mappings from API response
          if (data.mapping && data.mapping.length > 0) {
            onChange({
              ...definition,
              mapping: data.mapping as ImportMapping[]
            })
          }
        })
        .catch(err => console.error('Failed to load columns:', err))
        .finally(() => setLoading(false))
    }
  }, [definition.id, definition.class])

  const mappings = definition.mapping || []

  // Group mappings by their path/type
  const getGroupKey = (toColumn: string | undefined): string => {
    if (!toColumn) return 'fields'
    // Check for localized fields (e.g., localizedfield.en)
    if (toColumn.includes('~')) {
      const parts = toColumn.split('~')
      return parts[0] // e.g., "localizedfield.en"
    }
    // Check for classification store
    if (toColumn.startsWith('classificationstore~')) {
      const match = toColumn.match(/classificationstore~([^~]+)/)
      if (match) return `classificationstore - ${match[1]}`
    }
    // Check for object bricks
    if (toColumn.startsWith('objectbrick~')) {
      const match = toColumn.match(/objectbrick~([^~]+)/)
      if (match) return `objectbrick - ${match[1]}`
    }
    // Check for field collections
    if (toColumn.startsWith('fieldcollection~')) {
      const match = toColumn.match(/fieldcollection~([^~]+)/)
      if (match) return `fieldcollection - ${match[1]}`
    }
    // System columns
    const systemFields = ['id', 'key', 'parentId', 'parent', 'type', 'published', 'o_id', 'o_key', 'o_parentId', 'o_parent', 'o_type', 'o_published', 'o_path', 'o_className']
    if (systemFields.includes(toColumn) || toColumn.startsWith('o_')) {
      return 'systemColumn'
    }
    return 'fields'
  }

  const getFieldName = (toColumn: string): string => {
    // Extract just the field name from complex paths
    if (toColumn.includes('~')) {
      const parts = toColumn.split('~')
      return parts[parts.length - 1]
    }
    return toColumn
  }

  // Build grouped data for the table
  const buildGroupedData = (): GroupedMapping[] => {
    const groups: Record<string, { mappings: Array<{ mapping: ImportMapping; index: number }> }> = {}

    mappings.forEach((mapping, index) => {
      const groupKey = getGroupKey(mapping.toColumn)
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
          const colDef = toColumns.find(c => c.identifier === mapping.toColumn)
          result.push({
            key: `mapping-${index}`,
            isGroup: false,
            mapping,
            mappingIndex: index,
            toColumn: mapping.toColumn,
            fromColumn: mapping.fromColumn,
            primaryIdentifier: mapping.primaryIdentifier,
            fieldtype: colDef?.fieldtype,
            hasInterpreter: !!mapping.interpreter,
            hasSetter: !!mapping.setter
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

  const handleFromColumnChange = (index: number, value: string | undefined) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], fromColumn: value }
    onChange({ ...definition, mapping: newMappings })
  }

  const handlePrimaryChange = (index: number, checked: boolean) => {
    const newMappings = [...mappings]
    newMappings[index] = { ...newMappings[index], primaryIdentifier: checked }
    onChange({ ...definition, mapping: newMappings })
  }

  const handleOpenConfig = (index: number) => {
    setSelectedMappingIndex(index)
    setConfigDialogVisible(true)
  }

  const handleSaveConfig = (updatedMapping: ImportMapping) => {
    if (selectedMappingIndex !== null) {
      const newMappings = [...mappings]
      newMappings[selectedMappingIndex] = updatedMapping
      onChange({ ...definition, mapping: newMappings })
    }
    setConfigDialogVisible(false)
    setSelectedMappingIndex(null)
  }

  const handleRemoveMapping = (index: number) => {
    const newMappings = mappings.filter((_, i) => i !== index)
    onChange({ ...definition, mapping: newMappings })
  }

  // From column options for dropdown
  const fromColumnOptions = [
    { value: '', label: '-- None --' },
    { value: 'custom', label: 'Custom' },
    ...fromColumns.map(col => ({
      value: col.identifier,
      label: col.label || col.identifier
    }))
  ]

  const columns = [
    {
      title: t('data_definitions.to_column'),
      dataIndex: 'toColumn',
      key: 'toColumn',
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
              {getFieldName(record.toColumn!)}
            </span>
          </div>
        )
      }
    },
    {
      title: t('data_definitions.from_column'),
      dataIndex: 'fromColumn',
      key: 'fromColumn',
      width: '35%',
      render: (_: any, record: GroupedMapping) => {
        if (record.isGroup) return null
        return (
          <Select
            size="small"
            style={{ width: '100%' }}
            value={record.fromColumn || ''}
            onChange={(value) => handleFromColumnChange(record.mappingIndex!, value || undefined)}
            options={fromColumnOptions}
            allowClear
            showSearch
            filterOption={(input, option) =>
              (option?.label ?? '').toString().toLowerCase().includes(input.toLowerCase())
            }
          />
        )
      }
    },
    {
      title: t('data_definitions.primary'),
      dataIndex: 'primaryIdentifier',
      key: 'primaryIdentifier',
      width: '10%',
      align: 'center' as const,
      render: (_: any, record: GroupedMapping) => {
        if (record.isGroup) return null
        return (
          <Checkbox
            checked={record.primaryIdentifier}
            onChange={(e) => handlePrimaryChange(record.mappingIndex!, e.target.checked)}
          />
        )
      }
    },
    {
      title: '',
      key: 'actions',
      width: '15%',
      align: 'right' as const,
      render: (_: any, record: GroupedMapping) => {
        if (record.isGroup) return null
        const hasConfig = record.hasInterpreter || record.hasSetter
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
          mode="import"
          fromColumns={fromColumns}
          toColumnLabel={mappings[selectedMappingIndex].toColumn}
          onSave={handleSaveConfig}
          onCancel={() => { setConfigDialogVisible(false); setSelectedMappingIndex(null) }}
        />
      )}
    </div>
  )
}
