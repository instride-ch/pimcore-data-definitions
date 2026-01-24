/**
 * Classification Store Setter Configuration
 */

import React from 'react'
import { Form, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import type { SetterConfigProps } from './index'

const { Text } = Typography

export const ClassificationStoreSetterConfig: React.FC<SetterConfigProps> = ({
  config,
  toColumnConfig,
  onChange
}) => {
  const { t } = useTranslation()

  // Classification store config is typically derived from the column
  // No additional user input needed, just display the values

  const keyId = toColumnConfig?.config?.keyId
  const groupId = toColumnConfig?.config?.groupId
  const field = toColumnConfig?.config?.field

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
      {keyId !== undefined && (
        <Form.Item label={t('data_definitions.setter.classification_store.key')}>
          <Text code>{keyId}</Text>
        </Form.Item>
      )}

      {groupId !== undefined && (
        <Form.Item label={t('data_definitions.setter.classification_store.group')}>
          <Text code>{groupId}</Text>
        </Form.Item>
      )}
    </div>
  )
}
