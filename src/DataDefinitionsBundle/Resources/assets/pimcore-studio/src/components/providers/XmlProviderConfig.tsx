/**
 * XML Provider Configuration
 */

import React from 'react'
import { Input, InputNumber, Alert } from 'antd'
import { CodeOutlined, FileTextOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProviderConfigProps } from './index'
import { useProviderStyles } from './ProviderConfig.styles'

const { TextArea } = Input

export const XmlProviderConfig: React.FC<ProviderConfigProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const { styles } = useProviderStyles()

  return (
    <div className={styles.container}>
      {/* XPath Settings */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <CodeOutlined className={styles.sectionIcon} />
          {t('data_definitions.xml.xpath_configuration')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.xml.xpath')}</label>
          <Input
            value={config.xPath || ''}
            onChange={e => onChange({ ...config, xPath: e.target.value })}
            placeholder="//product"
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.xml.xpath_help')}
          </div>
        </div>

        <Alert
          type="info"
          showIcon
          style={{ marginTop: 12 }}
          message={t('data_definitions.xml.xpath_examples')}
          description={
            <ul style={{ margin: '8px 0 0 0', paddingLeft: 20, fontSize: 12 }}>
              <li><code>//product</code> - All product elements anywhere</li>
              <li><code>/root/products/product</code> - Absolute path</li>
              <li><code>//item[@type='product']</code> - With attribute filter</li>
            </ul>
          }
        />
      </div>

      {/* Field Extraction */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <FileTextOutlined className={styles.sectionIcon} />
          {t('data_definitions.xml.field_extraction')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.xml.example_xpath')}</label>
          <Input
            value={config.exampleXPath || ''}
            onChange={e => onChange({ ...config, exampleXPath: e.target.value })}
            placeholder="@id or name/text()"
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.xml.example_xpath_help')}
          </div>
        </div>

        <div className={styles.row} style={{ marginTop: 16 }}>
          <div className={styles.fieldSmall}>
            <label className={styles.fieldLabel}>{t('data_definitions.xml.example_file')}</label>
            <InputNumber
              value={config.exampleFile}
              onChange={value => onChange({ ...config, exampleFile: value })}
              placeholder="Asset ID"
              min={1}
              style={{ width: '100%' }}
            />
            <div className={styles.fieldHelp}>{t('data_definitions.xml.example_file_help')}</div>
          </div>
        </div>
      </div>

      {/* XML Example */}
      <div className={styles.section}>
        <div className={styles.sectionTitle}>
          <FileTextOutlined className={styles.sectionIcon} />
          {t('data_definitions.xml.example_data')}
        </div>

        <div className={styles.field}>
          <label className={styles.fieldLabel}>{t('data_definitions.xml.sample')}</label>
          <TextArea
            value={config.xmlExample || ''}
            onChange={e => onChange({ ...config, xmlExample: e.target.value })}
            placeholder={'<?xml version="1.0"?>\n<root>\n  <product id="1">\n    <name>Product 1</name>\n  </product>\n</root>'}
            rows={8}
            className={styles.codeEditor}
          />
          <div className={styles.fieldHelp}>
            {t('data_definitions.xml.sample_help')}
          </div>
        </div>
      </div>
    </div>
  )
}
