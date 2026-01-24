/**
 * SQL Provider Configuration
 * Uses the internal Pimcore database
 */

import React from 'react'
import { Input, Alert } from 'antd'
import { DatabaseOutlined, CodeOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const SqlProviderConfig: React.FC<ProviderConfigProps> = ({
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
        icon={<DatabaseOutlined />}
        message={t('data_definitions.sql.internal_database')}
        description={t('data_definitions.sql.internal_database_help')}
        style={{ marginBottom: 16 }}
      />

      {/* SQL Query */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <CodeOutlined className={styles.sectionIcon} />
          {t('data_definitions.sql.query')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.sql.query')}</label>
          <TextArea
            value={config.query || ''}
            onChange={e => onChange({ ...config, query: e.target.value })}
            placeholder={'SELECT \n  id,\n  name,\n  sku,\n  price\nFROM products\nWHERE active = 1'}
            rows={12}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.sql.query_help')}
          </div>
        </div>

        <Alert
          type="warning"
          showIcon
          style={{ marginTop: 12 }}
          message={t('data_definitions.sql.security_note')}
          description={t('data_definitions.sql.security_note_text')}
        />
      </div>
    </div>
  )
}
