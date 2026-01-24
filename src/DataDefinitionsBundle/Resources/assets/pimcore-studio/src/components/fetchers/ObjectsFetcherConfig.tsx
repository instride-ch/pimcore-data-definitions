/**
 * Data Definitions Bundle - Pimcore Studio Plugin
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    DDCL
 */

import React from 'react'
import { Form, Checkbox } from 'antd'
import type { FetcherConfigProps } from './index'

export const ObjectsFetcherConfig: React.FC<FetcherConfigProps> = ({ config, onChange }) => {
  const handleChange = (field: string, value: any): void => {
    onChange({ ...config, [field]: value })
  }

  return (
    <Form layout="vertical" size="small">
      <Form.Item label="Include Unpublished">
        <Checkbox
          checked={config.unpublished ?? false}
          onChange={(e) => handleChange('unpublished', e.target.checked)}
        >
          Fetch unpublished objects
        </Checkbox>
      </Form.Item>
    </Form>
  )
}
