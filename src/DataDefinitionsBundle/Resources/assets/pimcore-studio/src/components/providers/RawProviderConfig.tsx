/**
 * Raw Provider Configuration
 * For custom/programmatic data input
 */

import React from 'react'
import { Input, Alert } from 'antd'
import { CodeOutlined, InfoCircleOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const RawProviderConfig: React.FC<ProviderConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useProviderStyles()

  return (
    <div className={styles.container}>
      <Alert
        type="info"
        showIcon
        icon={<InfoCircleOutlined />}
        message={t('data_definitions.raw.title')}
        description={t('data_definitions.raw.description')}
        style={{ marginBottom: 16 }}
      />

      {/* Column Definition */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <CodeOutlined className={styles.sectionIcon} />
          {t('data_definitions.raw.column_definition')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.raw.headers')}</label>
          <TextArea
            value={config.headers || ''}
            onChange={e => onChange({ ...config, headers: e.target.value })}
            placeholder={'id\nname\nsku\nprice\ndescription\ncategory_id'}
            rows={10}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.raw.headers_help')}
          </div>
        </div>

        {config.headers && (
          <div className={styles.previewSection}>
            <div className={styles.previewLabel}>{t('data_definitions.raw.defined_columns')}</div>
            <div className={styles.previewContent}>
              {config.headers.split('\n').filter((h: string) => h.trim()).join(' | ')}
            </div>
          </div>
        )}
      </div>
    </div>
  )
}
