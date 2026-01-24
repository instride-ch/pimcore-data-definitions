/**
 * External SQL Provider Configuration
 * Connects to an external database
 */

import React from 'react'
import { Input, InputNumber, Select } from 'antd'
import { DatabaseOutlined, LockOutlined, CodeOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const ExternalSqlProviderConfig: React.FC<ProviderConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useProviderStyles()

  const adapterOptions = [
    { value: 'pdo_mysql', label: 'MySQL (pdo_mysql)' },
    { value: 'pdo_pgsql', label: 'PostgreSQL (pdo_pgsql)' },
    { value: 'pdo_sqlite', label: 'SQLite (pdo_sqlite)' },
    { value: 'pdo_sqlsrv', label: 'SQL Server (pdo_sqlsrv)' },
  ]

  return (
    <div className={styles.container}>
      {/* Connection Settings */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <DatabaseOutlined className={styles.sectionIcon} />
          {t('data_definitions.external_sql.connection')}
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <label className={styles.fieldLabel}>{t('data_definitions.external_sql.adapter')}</label>
            <Select
              value={config.adapter || 'pdo_mysql'}
              onChange={value => onChange({ ...config, adapter: value })}
              options={adapterOptions}
              style={{ width: '100%' }}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.external_sql.adapter_help')}</div>
          </div>
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <label className={styles.fieldLabel}>{t('data_definitions.external_sql.host')}</label>
            <Input
              value={config.host || ''}
              onChange={e => onChange({ ...config, host: e.target.value })}
              placeholder="localhost or 192.168.1.100"
            />
            <div className={styles.fieldHelp}>{t('data_definitions.external_sql.host_help')}</div>
          </div>

          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.external_sql.port')}</label>
            <InputNumber
              value={config.port || 3306}
              onChange={value => onChange({ ...config, port: value })}
              min={1}
              max={65535}
              style={{ width: '100%' }}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.external_sql.port_help')}</div>
          </div>
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <label className={styles.fieldLabel}>{t('data_definitions.external_sql.database')}</label>
            <Input
              value={config.database || ''}
              onChange={e => onChange({ ...config, database: e.target.value })}
              placeholder="my_database"
            />
          </div>
        </div>
      </div>

      {/* Credentials */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <LockOutlined className={styles.sectionIcon} />
          {t('data_definitions.external_sql.credentials')}
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <label className={styles.fieldLabel}>{t('data_definitions.external_sql.username')}</label>
            <Input
              value={config.username || ''}
              onChange={e => onChange({ ...config, username: e.target.value })}
              placeholder="db_user"
            />
          </div>

          <div className={styles.field}>
            <label className={styles.fieldLabel}>{t('data_definitions.external_sql.password')}</label>
            <Input.Password
              value={config.password || ''}
              onChange={e => onChange({ ...config, password: e.target.value })}
              placeholder="********"
            />
          </div>
        </div>
      </div>

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
            rows={10}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.sql.query_help')}
          </div>
        </div>
      </div>
    </div>
  )
}
