/**
 * Field Collection Setter Configuration
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Input, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import type { SetterConfigProps } from './index'

const { Text } = Typography

export const FieldCollectionSetterConfig: React.FC<SetterConfigProps> = ({
  config,
  definitionConfig,
  toColumnConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [possibleFields, setPossibleFields] = useState<string[]>([])

  useEffect(() => {
    // Get the field collection class from the target column config
    const fieldClass = toColumnConfig?.config?.class

    if (fieldClass && (definitionConfig as any).fieldcollections) {
      const fieldcollections = (definitionConfig as any).fieldcollections
      const fields: string[] = []

      Object.entries(fieldcollections).forEach(([key, value]) => {
        if (Array.isArray(value) && value.includes(fieldClass)) {
          fields.push(key)
        }
      })

      setPossibleFields(fields)
    }
  }, [toColumnConfig, definitionConfig])

  // Extract class from column config
  const collectionClass = toColumnConfig?.config?.class

  return (
    <div>
      {collectionClass && (
        <Form.Item label={t('data_definitions.setter.field_collection.class')}>
          <Text code>{collectionClass}</Text>
        </Form.Item>
      )}

      <Form.Item label={t('data_definitions.setter.field_collection.field')}>
        <Select
          value={config.field}
          onChange={value => onChange({ ...config, field: value, class: collectionClass })}
          allowClear
        >
          {possibleFields.map(f => (
            <Select.Option key={f} value={f}>{f}</Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label={t('data_definitions.setter.field_collection.keys')}>
        <Input
          value={config.keys || ''}
          onChange={e => onChange({ ...config, keys: e.target.value })}
          placeholder="key1,key2,key3"
        />
      </Form.Item>
    </div>
  )
}
