/**
 * Object Brick Getter Configuration
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Typography } from 'antd'
import type { GetterConfigProps } from './index'

const { Text } = Typography

export const ObjectBrickGetterConfig: React.FC<GetterConfigProps> = ({
  config,
  definitionConfig,
  fromColumnConfig,
  onChange
}) => {
  const [possibleFields, setPossibleFields] = useState<string[]>([])

  useEffect(() => {
    // Get the brick class from the source column config
    const fieldClass = fromColumnConfig?.config?.class

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
  }, [fromColumnConfig, definitionConfig])

  // Extract class from column config
  const brickClass = fromColumnConfig?.config?.class

  return (
    <div>
      {brickClass && (
        <Form.Item label="Brick Class">
          <Text code>{brickClass}</Text>
        </Form.Item>
      )}

      <Form.Item label="Brick Field">
        <Select
          value={config.brickField}
          onChange={value => onChange({ ...config, brickField: value, class: brickClass })}
          allowClear
          placeholder="Select brick field"
        >
          {possibleFields.map(f => (
            <Select.Option key={f} value={f}>{f}</Select.Option>
          ))}
        </Select>
      </Form.Item>
    </div>
  )
}
