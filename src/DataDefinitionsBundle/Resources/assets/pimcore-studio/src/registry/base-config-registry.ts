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

import type React from 'react'

/**
 * Generic registry for configuration components.
 * Allows external bundles/plugins to register their own React configuration components.
 */
export class ConfigRegistry<T> {
  private readonly components = new Map<string, React.ComponentType<T>>()

  /**
   * Register a configuration component for a given type
   */
  register(type: string, component: React.ComponentType<T>): void {
    this.components.set(type.toLowerCase(), component)
  }

  /**
   * Get a configuration component by type
   */
  get(type: string): React.ComponentType<T> | undefined {
    return this.components.get(type.toLowerCase())
  }

  /**
   * Check if a configuration component exists for a type
   */
  has(type: string): boolean {
    return this.components.has(type.toLowerCase())
  }

  /**
   * Get all registered type names
   */
  getAll(): string[] {
    return Array.from(this.components.keys())
  }

  /**
   * Unregister a configuration component
   */
  unregister(type: string): boolean {
    return this.components.delete(type.toLowerCase())
  }
}
