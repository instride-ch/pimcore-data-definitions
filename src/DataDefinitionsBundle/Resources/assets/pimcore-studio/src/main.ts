/**
 * Data Definitions Bundle - Pimcore Studio Plugin
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { DataDefinitionsMenuModule } from './modules/menu'
import { DataDefinitionsIconModule } from './modules/icon-library'

const DataDefinitionsPlugin: IAbstractPlugin = {
  name: 'data-definitions',

  onInit(): void {
    // Plugin initialization - can bind services here if needed
  },

  onStartup({ moduleSystem }): void {
    // Register icon library module
    moduleSystem.registerModule(DataDefinitionsIconModule)
    
    // Register menu module
    moduleSystem.registerModule(DataDefinitionsMenuModule)
  }
}

export default DataDefinitionsPlugin
