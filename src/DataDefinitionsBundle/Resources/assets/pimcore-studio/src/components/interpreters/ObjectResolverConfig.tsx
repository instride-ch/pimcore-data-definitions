/**
 * Object Resolver Interpreter Configuration
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Input, Switch, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const ObjectResolverConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [classes, setClasses] = useState<string[]>([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    loadClasses()
  }, [])

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
      <Spin spinning={loading}>
        <Form.Item label={t('data_definitions.interpreter.object_resolver.class')}>
          <Select
            value={config.class}
            onChange={value => onChange({ ...config, class: value })}
            allowClear
            showSearch
            options={classes.map(c => ({ value: c, label: c }))}
          />
        </Form.Item>
      </Spin>

      <Form.Item label={t('data_definitions.interpreter.object_resolver.match_field')}>
        <Input
          value={config.field || ''}
          onChange={e => onChange({ ...config, field: e.target.value })}
          placeholder="e.g., sku"
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.fetch_unpublished')}>
        <Switch
          checked={config.match_unpublished !== false}
          onChange={checked => onChange({ ...config, match_unpublished: checked })}
        />
      </Form.Item>
    </div>
  )
}
