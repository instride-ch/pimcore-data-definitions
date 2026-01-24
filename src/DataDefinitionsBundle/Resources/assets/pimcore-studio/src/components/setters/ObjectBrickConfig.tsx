/**
 * Object Brick Setter Configuration
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import type { SetterConfigProps } from './index'

const { Text } = Typography

export const ObjectBrickSetterConfig: React.FC<SetterConfigProps> = ({
  config,
  definitionConfig,
  toColumnConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [possibleFields, setPossibleFields] = useState<string[]>([])

  useEffect(() => {
    // Get the brick class from the target column config
    const fieldClass = toColumnConfig?.config?.class

    if (fieldClass && (definitionConfig as any).bricks) {
      const bricks = (definitionConfig as any).bricks
      const fields: string[] = []

      Object.entries(bricks).forEach(([key, value]) => {
        if (Array.isArray(value) && value.includes(fieldClass)) {
          fields.push(key)
        }
      })

      setPossibleFields(fields)
    }
  }, [toColumnConfig, definitionConfig])

  // Extract class from column config
  const brickClass = toColumnConfig?.config?.class

  return (
    <div>
      {brickClass && (
        <Form.Item label={t('data_definitions.setter.object_brick.class')}>
          <Text code>{brickClass}</Text>
        </Form.Item>
      )}

      <Form.Item label={t('data_definitions.setter.object_brick.field')}>
        <Select
          value={config.brickField}
          onChange={value => onChange({ ...config, brickField: value, class: brickClass })}
          allowClear
        >
          {possibleFields.map(f => (
            <Select.Option key={f} value={f}>{f}</Select.Option>
          ))}
        </Select>
      </Form.Item>
    </div>
  )
}
