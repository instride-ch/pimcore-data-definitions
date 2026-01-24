/**
 * Localized Field Getter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import type { GetterConfigProps } from './index'

export const LocalizedFieldGetterConfig: React.FC<GetterConfigProps> = ({
  config,
  onChange
}) => {
  return (
    <Form.Item label="Language" help="Language code (e.g., en, de, fr)">
      <Input
        value={config.language || ''}
        onChange={e => onChange({ ...config, language: e.target.value })}
        placeholder="en"
      />
    </Form.Item>
  )
}
