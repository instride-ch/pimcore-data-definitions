/**
 * Metadata Interpreter Configuration
 */

import React from 'react'
import { Form, Select, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

const classOptions = [
  { value: 'ElementMetadata', label: 'Element Metadata' },
  { value: 'ObjectMetadata', label: 'Object Metadata' }
]

export const MetadataConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <div>
      <Form.Item label={t('data_definitions.class')}>
        <Select
          value={config.class || 'ElementMetadata'}
          onChange={value => onChange({ ...config, class: value })}
          options={classOptions}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.interpreter.metadata.key')}>
        <Input
          value={config.metadata || ''}
          onChange={e => onChange({ ...config, metadata: e.target.value })}
        />
      </Form.Item>
    </div>
  )
}
