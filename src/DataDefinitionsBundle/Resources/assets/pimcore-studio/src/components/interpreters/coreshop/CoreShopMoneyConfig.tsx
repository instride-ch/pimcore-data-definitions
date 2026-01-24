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
import { Form, Checkbox, InputNumber } from 'antd'
import type { InterpreterConfigProps } from '../index'

/**
 * Configuration for CoreShop Money Interpreter
 * Converts values to CoreShop Money objects with currency
 */
export const CoreShopMoneyConfig: React.FC<InterpreterConfigProps> = ({ config, onChange }) => {
  const handleChange = (field: string, value: any): void => {
    onChange({ ...config, [field]: value })
  }

  return (
    <Form layout="vertical" size="small">
      <Form.Item label="Currency ID">
        <InputNumber
          style={{ width: '100%' }}
          placeholder="CoreShop Currency ID"
          value={config.currency}
          onChange={(value) => handleChange('currency', value)}
        />
      </Form.Item>
      <Form.Item>
        <Checkbox
          checked={config.isFloat ?? false}
          onChange={(e) => handleChange('isFloat', e.target.checked)}
        >
          Value is Float (not integer cents)
        </Checkbox>
      </Form.Item>
    </Form>
  )
}
