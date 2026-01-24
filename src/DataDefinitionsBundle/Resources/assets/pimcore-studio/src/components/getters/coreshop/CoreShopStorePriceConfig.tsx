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
import { Form, Select } from 'antd'
import type { GetterConfigProps } from '../index'

/**
 * Configuration for CoreShop Store Price Getter
 * Gets prices from specific CoreShop stores
 */
export const CoreShopStorePriceGetterConfig: React.FC<GetterConfigProps> = ({ config, onChange }) => {
  const handleChange = (field: string, value: any): void => {
    onChange({ ...config, [field]: value })
  }

  const storeIds = Array.isArray(config.stores) ? config.stores : []

  return (
    <Form layout="vertical" size="small">
      <Form.Item label="Store IDs">
        <Select
          mode="tags"
          style={{ width: '100%' }}
          placeholder="Enter CoreShop Store IDs"
          value={storeIds.map(String)}
          onChange={(values) => handleChange('stores', values.map(Number))}
          tokenSeparators={[',']}
        />
      </Form.Item>
    </Form>
  )
}
