/**
 * Asset By Path Interpreter Configuration
 */

import React from 'react'
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const AssetByPathConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <Form.Item label={t('data_definitions.interpreter.asset_by_path.path')}>
      <Input
        value={config.path || ''}
        onChange={e => onChange({ ...config, path: e.target.value })}
        placeholder="/path/to/asset"
      />
    </Form.Item>
  )
}
