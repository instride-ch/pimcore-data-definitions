/**
 * Quantity Value Interpreter Configuration
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

interface QuantityUnit {
  id: string
  abbreviation: string
}

export const QuantityValueConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [units, setUnits] = useState<QuantityUnit[]>([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    loadUnits()
  }, [])

  const loadUnits = async () => {
    setLoading(true)
    try {
      const response = await fetch('/pimcore-studio/api/quantity-value/unit')
      const data = await response.json()
      if (data.items && Array.isArray(data.items)) {
        setUnits(data.items)
      } else if (data.data && Array.isArray(data.data)) {
        setUnits(data.data)
      }
    } catch (error) {
      console.error('Failed to load units:', error)
    } finally {
      setLoading(false)
    }
  }

  return (
    <Spin spinning={loading}>
      <Form.Item label={t('data_definitions.interpreter.quantity_value.unit')}>
        <Select
          value={config.unit}
          onChange={value => onChange({ ...config, unit: value })}
          allowClear
          showSearch
          options={units.map(u => ({
            value: u.id,
            label: `${u.abbreviation} (${u.id})`
          }))}
        />
      </Form.Item>
    </Spin>
  )
}
