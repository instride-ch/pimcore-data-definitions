/**
 * Field Collection Getter Configuration
 */

import React from 'react'
import { Form, Input, Typography } from 'antd'
import type { GetterConfigProps } from './index'

const { Text } = Typography

export const FieldCollectionGetterConfig: React.FC<GetterConfigProps> = ({
  config,
  fromColumnConfig,
  onChange
}) => {
  // Extract class from column config
  const collectionClass = fromColumnConfig?.config?.class

  return (
    <div>
      {collectionClass && (
        <Form.Item label="Field Collection Class">
          <Text code>{collectionClass}</Text>
        </Form.Item>
      )}

      <Form.Item label="Field" help="Field name to retrieve">
        <Input
          value={config.field || ''}
          onChange={e => onChange({ ...config, field: e.target.value, class: collectionClass })}
          placeholder="fieldName"
        />
      </Form.Item>
    </div>
  )
}
