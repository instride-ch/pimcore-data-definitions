/**
 * Conditional Interpreter Configuration
 *
 * Executes different interpreters based on a condition
 */

import React, { useState } from 'react'
import { Form, Input, Select, Card } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

interface NestedInterpreterConfig {
  type?: string
  interpreterConfig?: Record<string, any>
}

export const ConditionalConfig: React.FC<InterpreterConfigProps> = ({
  config,
  definitionConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [trueInterpreter, setTrueInterpreter] = useState<NestedInterpreterConfig>(
    config.true_interpreter || {}
  )
  const [falseInterpreter, setFalseInterpreter] = useState<NestedInterpreterConfig>(
    config.false_interpreter || {}
  )

  const updateTrueInterpreter = (type: string) => {
    const newConfig = { type, interpreterConfig: {} }
    setTrueInterpreter(newConfig)
    onChange({
      ...config,
      true_interpreter: newConfig
    })
  }

  const updateFalseInterpreter = (type: string) => {
    const newConfig = { type, interpreterConfig: {} }
    setFalseInterpreter(newConfig)
    onChange({
      ...config,
      false_interpreter: newConfig
    })
  }

  return (
    <div>
      <Form.Item label={t('data_definitions.interpreter.conditional.condition')}>
        <Input.TextArea
          value={config.condition || ''}
          onChange={e => onChange({ ...config, condition: e.target.value })}
          placeholder="e.g., value > 0"
          rows={2}
        />
      </Form.Item>

      <Card size="small" title={t('data_definitions.interpreter.conditional.true_interpreter')} style={{ marginBottom: 16 }}>
        <Form.Item label={t('data_definitions.interpreter')}>
          <Select
            value={trueInterpreter.type}
            onChange={updateTrueInterpreter}
            allowClear
            placeholder={t('data_definitions.select_interpreter')}
          >
            {definitionConfig?.interpreter?.map(i => (
              <Select.Option key={i} value={i}>{i}</Select.Option>
            ))}
          </Select>
        </Form.Item>
      </Card>

      <Card size="small" title={t('data_definitions.interpreter.conditional.false_interpreter')}>
        <Form.Item label={t('data_definitions.interpreter')}>
          <Select
            value={falseInterpreter.type}
            onChange={updateFalseInterpreter}
            allowClear
            placeholder={t('data_definitions.select_interpreter')}
          >
            {definitionConfig?.interpreter?.map(i => (
              <Select.Option key={i} value={i}>{i}</Select.Option>
            ))}
          </Select>
        </Form.Item>
      </Card>
    </div>
  )
}
