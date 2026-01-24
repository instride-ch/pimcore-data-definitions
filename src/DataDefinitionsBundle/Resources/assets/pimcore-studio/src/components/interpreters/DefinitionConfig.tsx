/**
 * Definition Interpreter Configuration
 *
 * References another import definition to use as sub-import
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'
import { importDefinitionApi } from '../../services/api'

interface ImportDefinitionOption {
  id: number
  name: string
}

export const DefinitionConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [definitions, setDefinitions] = useState<ImportDefinitionOption[]>([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    loadDefinitions()
  }, [])

  const loadDefinitions = async () => {
    setLoading(true)
    try {
      const data = await importDefinitionApi.list()
      setDefinitions(data.map((d: any) => ({ id: d.id, name: d.name })))
    } catch (error) {
      console.error('Failed to load definitions:', error)
    } finally {
      setLoading(false)
    }
  }

  return (
    <Spin spinning={loading}>
      <Form.Item label={t('data_definitions.interpreter.definition.definition')}>
        <Select
          value={config.definition}
          onChange={value => onChange({ ...config, definition: value })}
          allowClear
          showSearch
          optionFilterProp="label"
          options={definitions.map(d => ({
            value: d.id,
            label: d.name
          }))}
        />
      </Form.Item>
    </Spin>
  )
}
