/**
 * Mapping Configuration Dialog
 * Modal for configuring interpreter, setter, and getter for a mapping
 */

import React from 'react'
import { Modal, Form, Input, Select, Tabs, Button } from 'antd'
import { CheckOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ImportMapping, ExportMapping, DefinitionConfig, ColumnDefinition } from '../../types/definitions'
import { InterpreterConfig } from '../interpreters'
import { SetterConfig } from '../setters'
import { GetterConfig } from '../getters'
import { useStyles } from './MappingConfigDialog.styles'

interface MappingConfigDialogProps {
  visible: boolean
  mapping: ImportMapping | ExportMapping
  config: DefinitionConfig
  mode: 'import' | 'export'
  fromColumns?: ColumnDefinition[]
  fromColumnLabel?: string
  toColumnLabel?: string
  onSave: (mapping: ImportMapping | ExportMapping) => void
  onCancel: () => void
}

export const MappingConfigDialog: React.FC<MappingConfigDialogProps> = ({
  visible,
  mapping,
  config,
  mode,
  fromColumns = [],
  fromColumnLabel,
  toColumnLabel,
  onSave,
  onCancel
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const [localMapping, setLocalMapping] = React.useState(mapping)

  // Reset local state when mapping changes
  React.useEffect(() => {
    setLocalMapping(mapping)
  }, [mapping])

  const handleFieldChange = (field: string, value: any) => {
    setLocalMapping(prev => ({ ...prev, [field]: value }))
  }

  const handleInterpreterChange = (interpreter: string | undefined) => {
    setLocalMapping(prev => ({
      ...prev,
      interpreter,
      interpreterConfig: interpreter ? (prev.interpreterConfig || {}) : undefined
    }))
  }

  const handleSetterChange = (setter: string | undefined) => {
    setLocalMapping(prev => ({
      ...prev,
      setter,
      setterConfig: setter ? (prev.setterConfig || {}) : undefined
    }))
  }

  const handleGetterChange = (getter: string | undefined) => {
    setLocalMapping(prev => ({
      ...prev,
      getter,
      getterConfig: getter ? (prev.getterConfig || {}) : undefined
    }))
  }

  const handleApply = () => {
    onSave(localMapping)
  }

  const title = toColumnLabel
    ? t('data_definitions.field_config', { field: toColumnLabel })
    : t('data_definitions.mapping_config')

  return (
    <Modal
      title={title}
      open={visible}
      onCancel={onCancel}
      width={700}
      footer={
        <Button type="primary" icon={<CheckOutlined />} onClick={handleApply}>
          {t('data_definitions.apply')}
        </Button>
      }
    >
      <Tabs
        defaultActiveKey="settings"
        items={[
          {
            key: 'settings',
            label: t('data_definitions.settings'),
            children: (
              <div className={styles.tabContent}>
                <Form layout="horizontal" labelCol={{ span: 6 }} wrapperCol={{ span: 16 }}>
                  <Form.Item label={t('data_definitions.key')}>
                    <Input value={localMapping.toColumn} disabled />
                  </Form.Item>

                  {mode === 'import' && (
                    <Form.Item label={t('data_definitions.from_column')}>
                      <Select
                        value={localMapping.fromColumn}
                        onChange={v => handleFieldChange('fromColumn', v)}
                        allowClear
                        showSearch
                        placeholder={t('data_definitions.select_column')}
                      >
                        {fromColumns.map(col => (
                          <Select.Option key={col.identifier} value={col.identifier}>
                            {col.label || col.identifier}
                          </Select.Option>
                        ))}
                      </Select>
                    </Form.Item>
                  )}

                  {/* Getter Configuration (for export) */}
                  {mode === 'export' && (
                    <div className={styles.configSection}>
                      <div className={styles.configSectionTitle}>
                        {t('data_definitions.getter_configuration')}
                      </div>
                      <Form.Item label={t('data_definitions.getter_class')}>
                        <Select
                          value={localMapping.getter}
                          onChange={handleGetterChange}
                          allowClear
                          placeholder={t('data_definitions.select_getter')}
                        >
                          {config.getter?.map(g => (
                            <Select.Option key={g} value={g}>{g}</Select.Option>
                          ))}
                        </Select>
                      </Form.Item>
                      {localMapping.getter && (
                        <GetterConfig
                          type={localMapping.getter}
                          config={localMapping.getterConfig || {}}
                          onChange={cfg => handleFieldChange('getterConfig', cfg)}
                        />
                      )}
                    </div>
                  )}

                  {/* Setter Configuration (for import) */}
                  {mode === 'import' && (
                    <div className={styles.configSection}>
                      <div className={styles.configSectionTitle}>
                        {t('data_definitions.setter_configuration')}
                      </div>
                      <Form.Item label={t('data_definitions.setter_class')}>
                        <Select
                          value={localMapping.setter}
                          onChange={handleSetterChange}
                          allowClear
                          placeholder={t('data_definitions.select_setter')}
                        >
                          {config.setter?.map(s => (
                            <Select.Option key={s} value={s}>{s}</Select.Option>
                          ))}
                        </Select>
                      </Form.Item>
                      {localMapping.setter && (
                        <SetterConfig
                          type={localMapping.setter}
                          config={localMapping.setterConfig || {}}
                          onChange={cfg => handleFieldChange('setterConfig', cfg)}
                        />
                      )}
                    </div>
                  )}

                  {/* Interpreter Configuration */}
                  <div className={styles.configSection}>
                    <div className={styles.configSectionTitle}>
                      {t('data_definitions.interpreter_configuration')}
                    </div>
                    <Form.Item label={t('data_definitions.interpreter')}>
                      <Select
                        value={localMapping.interpreter}
                        onChange={handleInterpreterChange}
                        allowClear
                        placeholder={t('data_definitions.select_interpreter')}
                      >
                        {config.interpreter?.map(i => (
                          <Select.Option key={i} value={i}>{i}</Select.Option>
                        ))}
                      </Select>
                    </Form.Item>
                    {localMapping.interpreter && (
                      <InterpreterConfig
                        type={localMapping.interpreter}
                        config={localMapping.interpreterConfig || {}}
                        onChange={cfg => handleFieldChange('interpreterConfig', cfg)}
                        definitionConfig={config}
                      />
                    )}
                  </div>
                </Form>
              </div>
            )
          }
        ]}
      />
    </Modal>
  )
}
