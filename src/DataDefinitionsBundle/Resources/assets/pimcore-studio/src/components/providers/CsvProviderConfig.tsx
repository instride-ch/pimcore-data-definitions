/**
 * CSV Provider Configuration
 */

import React from 'react'
import { Input, Checkbox } from 'antd'
import {
  FileTextOutlined,
  SettingOutlined,
  DatabaseOutlined
} from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const CsvProviderConfig: React.FC<ProviderConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useProviderStyles()

  return (
    <div className={styles.container}>
      {/* Basic Settings */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <SettingOutlined className={styles.sectionIcon} />
          {t('data_definitions.csv.format_settings')}
        </div>

        <div className={styles.row}>
          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.csv.delimiter')}</label>
            <Input
              value={config.delimiter || ','}
              onChange={e => onChange({ ...config, delimiter: e.target.value })}
              placeholder=","
              maxLength={1}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.csv.delimiter_help')}</div>
          </div>

          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.csv.enclosure')}</label>
            <Input
              value={config.enclosure || '"'}
              onChange={e => onChange({ ...config, enclosure: e.target.value })}
              placeholder='"'
              maxLength={1}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.csv.enclosure_help')}</div>
          </div>

          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.csv.escape')}</label>
            <Input
              value={config.escape || '\\'}
              onChange={e => onChange({ ...config, escape: e.target.value })}
              placeholder="\"
              maxLength={1}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.csv.escape_help')}</div>
          </div>
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <Checkbox
              checked={config.skipFirstRow ?? true}
              onChange={e => onChange({ ...config, skipFirstRow: e.target.checked })}
            >
              {t('data_definitions.csv.skip_first_row')}
            </Checkbox>
          </div>
        </div>
      </div>

      {/* Data Source */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <FileTextOutlined className={styles.sectionIcon} />
          {t('data_definitions.csv.data')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.csv.example_data')}</label>
          <TextArea
            value={config.csvExample || ''}
            onChange={e => onChange({ ...config, csvExample: e.target.value })}
            placeholder={'name,category,price\nProduct 1,Electronics,99.99\nProduct 2,Clothing,49.99'}
            rows={6}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.csv.example_data_help')}
          </div>
        </div>

        {config.csvExample && (
          <div className={styles.previewSection}>
            <div className={styles.previewLabel}>{t('data_definitions.csv.detected_columns')}</div>
            <div className={styles.previewContent}>
              {(() => {
                try {
                  const delimiter = config.delimiter || ','
                  const firstLine = config.csvExample.split('\n')[0]
                  const columns = firstLine.split(delimiter).map((c: string) => c.trim().replace(/^["']|["']$/g, ''))
                  return columns.join(' | ')
                } catch {
                  return 'Unable to parse CSV'
                }
              })()}
            </div>
          </div>
        )}
      </div>

      {/* Manual Headers */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <DatabaseOutlined className={styles.sectionIcon} />
          {t('data_definitions.csv.manual_headers')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.csv.headers')}</label>
          <TextArea
            value={config.csvHeaders || ''}
            onChange={e => onChange({ ...config, csvHeaders: e.target.value })}
            placeholder={'column1\ncolumn2\ncolumn3'}
            rows={4}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.csv.headers_help')}
          </div>
        </div>
      </div>
    </div>
  )
}
