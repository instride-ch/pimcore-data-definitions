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
import { Form, InputNumber } from 'antd'
import type { RuleActionConfigProps } from '../index'

/**
 * Configuration for Object Rule Action
 * Sets or appends a specific Pimcore DataObject to the value.
 * If current value is an array, the object is appended.
 * Otherwise, it replaces the value.
 */
export const ObjectActionConfig: React.FC<RuleActionConfigProps> = ({ config, onChange }) => {
  const handleChange = (field: string, value: any): void => {
    onChange({ ...config, [field]: value })
  }

  return (
    <Form layout="vertical" size="small">
      <Form.Item
        label="Object ID"
        tooltip="ID of the Pimcore DataObject to set/add"
      >
        <InputNumber
          style={{ width: '100%' }}
          placeholder="Enter Object ID"
          value={config.object}
          onChange={(value) => handleChange('object', value)}
        />
      </Form.Item>
    </Form>
  )
}
