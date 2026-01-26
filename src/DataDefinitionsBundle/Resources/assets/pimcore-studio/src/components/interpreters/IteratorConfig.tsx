/**
 * Iterator Interpreter Configuration
 *
 * Applies an interpreter to each item in an array
 */

import React, { useState } from 'react'
import { Form, Select, Card } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

interface NestedInterpreterConfig {
  type?: string
  interpreterConfig?: Record<string, any>
}

export const IteratorConfig: React.FC<InterpreterConfigProps> = ({
  config,
  definitionConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [interpreter, setInterpreter] = useState<NestedInterpreterConfig>(
    config.interpreter || {}
  )

  const updateInterpreter = (type: string) => {
    const newConfig = { type, interpreterConfig: {} }
    setInterpreter(newConfig)
    onChange({
      ...config,
      interpreter: newConfig
    })
  }

  return (
    <Card size="small" title={t('data_definitions.interpreter.iterator.interpreter')} style={{ marginBottom: 16 }}>
      <Form.Item label={t('data_definitions.interpreter')}>
        <Select
          value={interpreter.type}
          onChange={updateInterpreter}
          allowClear
          placeholder={t('data_definitions.select_interpreter')}
        >
          {definitionConfig?.interpreter?.map(i => (
            <Select.Option key={i} value={i}>{i}</Select.Option>
          ))}
        </Select>
      </Form.Item>
    </Card>
  )
}
