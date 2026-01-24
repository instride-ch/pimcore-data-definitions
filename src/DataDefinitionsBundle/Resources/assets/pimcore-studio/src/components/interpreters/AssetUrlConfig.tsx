/**
 * Asset URL / Assets URL Interpreter Configuration
 */

import React from 'react'
import { Form, Input, Switch } from 'antd'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'

export const AssetUrlConfig: React.FC<InterpreterConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const deduplicateEnabled = config.deduplicate_by_url || config.deduplicate_by_hash

  return (
    <div>
      <Form.Item label={t('data_definitions.interpreter.asset_url.path')}>
        <Input
          value={config.path || ''}
          onChange={e => onChange({ ...config, path: e.target.value })}
          placeholder="/path/to/assets"
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.interpreter.asset_url.deduplicate')}>
        <Switch
          checked={config.deduplicate_by_url || false}
          onChange={checked => onChange({ ...config, deduplicate_by_url: checked })}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.interpreter.asset_url.asset_folder')}>
        <Input
          value={config.asset_folder || ''}
          onChange={e => onChange({ ...config, asset_folder: e.target.value })}
          placeholder="/assets"
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.relocate_existing_objects')}>
        <Switch
          checked={config.relocate_existing_objects || false}
          onChange={checked => onChange({ ...config, relocate_existing_objects: checked })}
          disabled={!deduplicateEnabled}
        />
      </Form.Item>

      <Form.Item label={t('data_definitions.rename_existing_objects')}>
        <Switch
          checked={config.rename_existing_objects || false}
          onChange={checked => onChange({ ...config, rename_existing_objects: checked })}
          disabled={!deduplicateEnabled}
        />
      </Form.Item>
    </div>
  )
}
