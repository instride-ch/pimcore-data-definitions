/**
 * Carbon (Date) Interpreter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const CarbonConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item
      label={t('data_definitions.interpreter.carbon.format')}
      help={t('data_definitions.interpreter.carbon.format_help')}
    >
      <Input
        value={config.date_format || ''}
        onChange={e => onChange({ ...config, date_format: e.target.value })}
        placeholder="Y-m-d H:i:s"
      />
    </Form.Item>
  )
}
