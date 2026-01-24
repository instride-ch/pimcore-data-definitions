/**
 * Localized Field Setter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { SetterConfigProps } from './index'

export const LocalizedFieldSetterConfig: React.FC<SetterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item label={t('data_definitions.setter.localized_field.language')}>
      <Input
        value={config.language || ''}
        onChange={e => onChange({ ...config, language: e.target.value })}
        placeholder="en"
      />
    </Form.Item>
  )
}
