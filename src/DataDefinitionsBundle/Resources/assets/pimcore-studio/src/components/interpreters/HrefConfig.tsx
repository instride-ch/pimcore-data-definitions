/**
 * Href / Multi Href Interpreter Configuration
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

const typeOptions = [
  { value: 'object', label: 'Object' },
  { value: 'asset', label: 'Asset' },
  { value: 'document', label: 'Document' }
]

export const HrefConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [classes, setClasses] = useState<string[]>([])
  const [loading, setLoading] = useState(false)
  const hrefType = config.type || 'object'

  useEffect(() => {
    if (hrefType === 'object') {
      loadClasses()
    }
  }, [hrefType])

  const loadClasses = async () => {
    setLoading(true)
    try {
      const response = await fetch('/pimcore-studio/api/class')
      const data = await response.json()
      if (data.items && Array.isArray(data.items)) {
        setClasses(data.items.map((c: any) => c.name || c.id))
      } else if (Array.isArray(data)) {
        setClasses(data.map((c: any) => c.name || c.text || c.id))
      }
    } catch (error) {
      console.error('Failed to load classes:', error)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <Form.Item label="Type">
        <Select
          value={hrefType}
          onChange={value => onChange({ ...config, type: value, class: undefined })}
          options={typeOptions}
        />
      </Form.Item>

      {hrefType === 'object' && (
        <Form.Item label={t('data_definitions.interpreter.href.class')}>
          <Spin spinning={loading}>
            <Select
              value={config.class}
              onChange={value => onChange({ ...config, class: value })}
              allowClear
              showSearch
              options={classes.map(c => ({ value: c, label: c }))}
            />
          </Spin>
        </Form.Item>
      )}
    </div>
  )
}
