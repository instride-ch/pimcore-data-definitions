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
import { Typography } from 'antd'

const { Text } = Typography

export interface NoConfigProps {
  type?: string
  message?: string
}

/**
 * Placeholder component for types that don't require configuration.
 * Can be used for interpreters, setters, getters, etc. that have no options.
 */
export const NoConfig: React.FC<NoConfigProps> = ({ message }) => {
  if (message) {
    return <Text type="secondary">{message}</Text>
  }
  return null
}
