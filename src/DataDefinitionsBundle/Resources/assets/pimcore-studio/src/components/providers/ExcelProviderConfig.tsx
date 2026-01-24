/**
 * Excel Provider Configuration
 */

import React from 'react'
import { Input, InputNumber, Checkbox, Alert } from 'antd'
import { FileExcelOutlined, TableOutlined, SettingOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const ExcelProviderConfig: React.FC<ProviderConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useProviderStyles()

  return (
    <div className={styles.container}>
      {/* File Settings */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <FileExcelOutlined className={styles.sectionIcon} />
          {t('data_definitions.excel.file')}
        </div>

        <div className={styles.row}>
          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.excel.example_file')}</label>
            <InputNumber
              value={config.exampleFile}
              onChange={value => onChange({ ...config, exampleFile: value })}
              placeholder="Asset ID"
              min={1}
              style={{ width: '100%' }}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.excel.example_file_help')}</div>
          </div>

          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.excel.sheet')}</label>
            <Input
              value={config.sheetName || ''}
              onChange={e => onChange({ ...config, sheetName: e.target.value })}
              placeholder="Sheet1 or 0"
            />
            <div className={styles.fieldHelp}>{t('data_definitions.excel.sheet_help')}</div>
          </div>
        </div>

        <Alert
          type="info"
          showIcon
          style={{ marginTop: 12 }}
          message={t('data_definitions.excel.supported_formats')}
          description={t('data_definitions.excel.supported_formats_help')}
        />
      </div>

      {/* Format Settings */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <SettingOutlined className={styles.sectionIcon} />
          {t('data_definitions.excel.settings')}
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <Checkbox
              checked={config.skipFirstRow ?? true}
              onChange={e => onChange({ ...config, skipFirstRow: e.target.checked })}
            >
              {t('data_definitions.excel.first_row_headers')}
            </Checkbox>
          </div>
        </div>
      </div>

      {/* Headers Override */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <TableOutlined className={styles.sectionIcon} />
          {t('data_definitions.excel.headers_override')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.excel.custom_headers')}</label>
          <TextArea
            value={config.excelHeaders || ''}
            onChange={e => onChange({ ...config, excelHeaders: e.target.value })}
            placeholder={'id\nname\nsku\nprice'}
            rows={6}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.excel.custom_headers_help')}
          </div>
        </div>
      </div>
    </div>
  )
}
