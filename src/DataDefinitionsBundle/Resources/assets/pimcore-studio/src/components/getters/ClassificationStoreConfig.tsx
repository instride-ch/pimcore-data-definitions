/**
 * Classification Store Getter Configuration
 */

import React from 'react'
import { Form, Typography } from 'antd'
import type { GetterConfigProps } from './index'

const { Text } = Typography

export const ClassificationStoreGetterConfig: React.FC<GetterConfigProps> = ({
  config,
  fromColumnConfig,
  onChange
}) => {
  // Classification store config is typically derived from the column
  // No additional user input needed, just display the values

  const keyId = fromColumnConfig?.config?.keyId
  const groupId = fromColumnConfig?.config?.groupId
  const field = fromColumnConfig?.config?.field

  // Update config with column values when they change
  React.useEffect(() => {
    if (keyId !== undefined || groupId !== undefined || field !== undefined) {
      onChange({
        ...config,
        keyConfig: keyId,
        groupConfig: groupId,
        field: field
      })
    }
  }, [keyId, groupId, field])

  return (
    <div>
      <Form.Item label="Configuration">
        <Text type="secondary">
          Classification store configuration is derived from the source column.
        </Text>
      </Form.Item>

      {keyId !== undefined && (
        <Form.Item label="Key ID">
          <Text code>{keyId}</Text>
        </Form.Item>
      )}

      {groupId !== undefined && (
        <Form.Item label="Group ID">
          <Text code>{groupId}</Text>
        </Form.Item>
      )}

      {field && (
        <Form.Item label="Field">
          <Text code>{field}</Text>
        </Form.Item>
      )}
    </div>
  )
}
