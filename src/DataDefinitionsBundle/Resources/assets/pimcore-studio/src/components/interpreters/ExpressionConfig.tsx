/**
 * Expression Interpreter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const ExpressionConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item
      label={t('data_definitions.interpreter.expression.expression')}
      help={t('data_definitions.interpreter.expression.expression_help')}
    >
      <Input.TextArea
        value={config.expression || ''}
        onChange={e => onChange({ ...config, expression: e.target.value })}
        placeholder="e.g., value > 0 ? value : null"
        rows={3}
      />
    </Form.Item>
  )
}
