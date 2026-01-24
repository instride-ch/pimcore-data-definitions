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
import { Form, Input } from 'antd'
import type { RuleActionConfigProps } from '../index'

const { TextArea } = Input

/**
 * Configuration for Expression Rule Action
 * Executes a Symfony Expression Language expression and returns the result.
 *
 * Available variables in expression:
 * - value: Current import value
 * - object: Pimcore Concrete object
 * - map: Current mapping configuration
 * - data: Current data row
 * - params: Additional parameters
 * - data_set: Full dataset
 * - container: Service container
 */
export const ExpressionActionConfig: React.FC<RuleActionConfigProps> = ({ config, onChange }) => {
  const handleChange = (field: string, value: any): void => {
    onChange({ ...config, [field]: value })
  }

  return (
    <Form layout="vertical" size="small">
      <Form.Item
        label="Expression"
        tooltip="Symfony Expression Language. Available: value, object, map, data, params, data_set, container"
      >
        <TextArea
          rows={3}
          placeholder="e.g. value ~ ' - modified' or object.getKey()"
          value={config.expression ?? ''}
          onChange={(e) => handleChange('expression', e.target.value)}
        />
      </Form.Item>
    </Form>
  )
}
