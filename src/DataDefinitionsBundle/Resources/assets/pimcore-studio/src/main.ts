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

import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { DataDefinitionsMenuModule } from './modules/menu'
import { DataDefinitionsIconModule } from './modules/icon-library'
import { DataDefinitionsRegistryModule } from './modules/registry-module'

const DataDefinitionsPlugin: IAbstractPlugin = {
  name: 'data-definitions',

  onInit(): void {
    // Initialize registries before other modules load
    DataDefinitionsRegistryModule.onInit()
  },

  onStartup({ moduleSystem }): void {
    // Register icon library module
    moduleSystem.registerModule(DataDefinitionsIconModule)

    // Register menu module
    moduleSystem.registerModule(DataDefinitionsMenuModule)
  }
}

export default DataDefinitionsPlugin
