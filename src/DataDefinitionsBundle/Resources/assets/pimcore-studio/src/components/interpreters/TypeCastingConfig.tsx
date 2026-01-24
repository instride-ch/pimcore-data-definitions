/**
 * Type Casting Interpreter Configuration
 */

import React from 'react'
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

const typeOptions = [
  { value: 'int', label: 'Integer' },
  { value: 'float', label: 'Float' },
  { value: 'string', label: 'String' },
  { value: 'boolean', label: 'Boolean' }
]

export const TypeCastingConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item label={t('data_definitions.interpreter.type_casting.type')}>
      <Select
        value={config.toType || 'int'}
        onChange={value => onChange({ ...config, toType: value })}
        options={typeOptions}
      />
    </Form.Item>
  )
}
