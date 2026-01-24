/**
 * Twig Interpreter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const TwigConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item
      label={t('data_definitions.interpreter.twig.template')}
      help={t('data_definitions.interpreter.twig.template_help')}
    >
      <Input.TextArea
        value={config.template || ''}
        onChange={e => onChange({ ...config, template: e.target.value })}
        placeholder="e.g., {{ value | upper }}"
        rows={4}
      />
    </Form.Item>
  )
}
