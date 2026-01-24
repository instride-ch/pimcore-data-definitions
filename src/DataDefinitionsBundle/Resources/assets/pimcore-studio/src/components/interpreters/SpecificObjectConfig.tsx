/**
 * Specific Object Interpreter Configuration
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const SpecificObjectConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item label={t('data_definitions.interpreter.specific_object.object_id')}>
      <InputNumber
        value={config.objectId}
        onChange={value => onChange({ ...config, objectId: value })}
        min={1}
        style={{ width: '100%' }}
      />
    </Form.Item>
  )
}
