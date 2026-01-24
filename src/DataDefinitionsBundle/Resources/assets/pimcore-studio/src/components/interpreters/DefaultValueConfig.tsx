/**
 * Default Value Interpreter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const DefaultValueConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item label={t('data_definitions.interpreter.default_value.value')}>
      <Input
        value={config.value || ''}
        onChange={e => onChange({ ...config, value: e.target.value })}
      />
    </Form.Item>
  )
}
