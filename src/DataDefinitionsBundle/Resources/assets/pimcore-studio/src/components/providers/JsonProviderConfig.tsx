/**
 * JSON Provider Configuration
 */

import React from 'react'
import { Input, Alert } from 'antd'
import { CodeOutlined, FileTextOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const JsonProviderConfig: React.FC<ProviderConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useProviderStyles()

  const parseJson = () => {
    if (!config.jsonExample) return null
    try {
      const json = JSON.parse(config.jsonExample)
      const keys = Object.keys(json)
      return `Root keys: ${keys.join(', ')}`
    } catch {
      return 'Invalid JSON'
    }
  }

  return (
    <div className={styles.container}>
      {/* JSON Path */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <CodeOutlined className={styles.sectionIcon} />
          {t('data_definitions.json.path_expression')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.json.path')}</label>
          <Input
            value={config.jsonPath || ''}
            onChange={e => onChange({ ...config, jsonPath: e.target.value })}
            placeholder="$.data[*]"
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.json.path_help')}
          </div>
        </div>

        <Alert
          type="info"
          showIcon
          style={{ marginTop: 12 }}
          message={t('data_definitions.json.path_examples')}
          description={
            <ul style={{ margin: '8px 0 0 0', paddingLeft: 20, fontSize: 12 }}>
              <li><code>$[*]</code> - All items in root array</li>
              <li><code>$.data[*]</code> - All items in data array</li>
              <li><code>$.products[*]</code> - All items in products array</li>
              <li><code>$.response.items[*]</code> - Nested array</li>
            </ul>
          }
        />
      </div>

      {/* JSON Example */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <FileTextOutlined className={styles.sectionIcon} />
          {t('data_definitions.json.example_data')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.json.sample')}</label>
          <TextArea
            value={config.jsonExample || ''}
            onChange={e => onChange({ ...config, jsonExample: e.target.value })}
            placeholder={'{\n  "data": [\n    {"id": 1, "name": "Product 1"},\n    {"id": 2, "name": "Product 2"}\n  ]\n}'}
            rows={10}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.json.sample_help')}
          </div>
        </div>

        {config.jsonExample && (
          <div className={styles.previewSection}>
            <div className={styles.previewLabel}>{t('data_definitions.json.detected_structure')}</div>
            <div className={styles.previewContent}>{parseJson()}</div>
          </div>
        )}
      </div>
    </div>
  )
}
