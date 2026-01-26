/**
 * Nested Interpreter Configuration
 *
 * Chains multiple interpreters in sequence
 */

import React, { useState } from 'react'
import { Form, Select, Button, Card, Space } from 'antd'
import { DeleteOutlined, ArrowUpOutlined, ArrowDownOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

interface NestedInterpreterItem {
  type: string
  interpreterConfig: Record<string, any>
}

export const NestedConfig: React.FC<InterpreterConfigProps> = ({
  config,
  definitionConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [interpreters, setInterpreters] = useState<NestedInterpreterItem[]>(
    config.interpreters || []
  )

  const updateInterpreters = (newInterpreters: NestedInterpreterItem[]) => {
    setInterpreters(newInterpreters)
    onChange({ ...config, interpreters: newInterpreters })
  }

  const addInterpreter = (type: string) => {
    updateInterpreters([...interpreters, { type, interpreterConfig: {} }])
  }

  const removeInterpreter = (index: number) => {
    updateInterpreters(interpreters.filter((_, i) => i !== index))
  }

  const moveInterpreter = (index: number, direction: 'up' | 'down') => {
    const newIndex = direction === 'up' ? index - 1 : index + 1
    if (newIndex < 0 || newIndex >= interpreters.length) return

    const newInterpreters = [...interpreters]
    const [removed] = newInterpreters.splice(index, 1)
    newInterpreters.splice(newIndex, 0, removed)
    updateInterpreters(newInterpreters)
  }

  const updateInterpreterType = (index: number, type: string) => {
    const newInterpreters = [...interpreters]
    newInterpreters[index] = { type, interpreterConfig: {} }
    updateInterpreters(newInterpreters)
  }

  return (
    <div>
      <Form.Item label={t('data_definitions.interpreter.nested.add')}>
        <Select
          placeholder={t('data_definitions.select_interpreter')}
          onChange={addInterpreter}
          value={undefined}
          style={{ width: '100%', marginBottom: 16 }}
        >
          {definitionConfig?.interpreter
            ?.filter(i => i !== 'nested')
            .map(i => (
              <Select.Option key={i} value={i}>{i}</Select.Option>
            ))}
        </Select>
      </Form.Item>

      <Space direction="vertical" style={{ width: '100%' }}>
        {interpreters.map((interpreter, index) => (
          <Card
            key={index}
            size="small"
            title={`${index + 1}. ${interpreter.type}`}
            extra={
              <Space>
                <Button
                  type="text"
                  size="small"
                  icon={<ArrowUpOutlined />}
                  onClick={() => moveInterpreter(index, 'up')}
                  disabled={index === 0}
                />
                <Button
                  type="text"
                  size="small"
                  icon={<ArrowDownOutlined />}
                  onClick={() => moveInterpreter(index, 'down')}
                  disabled={index === interpreters.length - 1}
                />
                <Button
                  type="text"
                  size="small"
                  danger
                  icon={<DeleteOutlined />}
                  onClick={() => removeInterpreter(index)}
                />
              </Space>
            }
          >
            <Form.Item label={t('data_definitions.interpreter')}>
              <Select
                value={interpreter.type}
                onChange={type => updateInterpreterType(index, type)}
              >
                {definitionConfig?.interpreter
                  ?.filter(i => i !== 'nested')
                  .map(i => (
                    <Select.Option key={i} value={i}>{i}</Select.Option>
                  ))}
              </Select>
            </Form.Item>
          </Card>
        ))}
      </Space>
    </div>
  )
}
