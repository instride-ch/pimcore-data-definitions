/**
 * Data Definitions - Menu Module
 *
 * Registers navigation items for Import and Export Definitions in Pimcore Studio
 */

import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { MainNavRegistry, type IMainNavItem } from '@pimcore/studio-ui-bundle/modules/app'
import { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { ImportDefinitionManager } from '../components/import/ImportDefinitionManager'
import { ExportDefinitionManager } from '../components/export/ExportDefinitionManager'

export const DataDefinitionsMenuModule = {
  onInit(): void {
    const mainNavRegistry = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)
    const widgetRegistry = container.get<WidgetRegistry>(serviceIds.widgetManager)

    // Register Import Definitions widget
    widgetRegistry.registerWidget({
      name: 'data-definitions-import',
      component: ImportDefinitionManager
    })

    // Register Export Definitions widget
    widgetRegistry.registerWidget({
      name: 'data-definitions-export',
      component: ExportDefinitionManager
    })

    // Register main navigation item for Data Definitions
    const dataDefinitionsNav: IMainNavItem = {
      path: 'Settings/Data Definitions'
    }
    mainNavRegistry.registerMainNavItem(dataDefinitionsNav)

    // Register Import Definitions navigation item
    const importNav: IMainNavItem = {
      path: 'Settings/Data Definitions/Import Definitions',
      icon: 'data_definitions_icon_import_definition',
      widgetConfig: {
        name: 'Import Definitions',
        id: 'data-definitions-import',
        component: 'data-definitions-import',
        config: {
          icon: {
            type: 'name',
            value: 'data_definitions_icon_import_definition'
          }
        }
      }
    }
    mainNavRegistry.registerMainNavItem(importNav)

    // Register Export Definitions navigation item
    const exportNav: IMainNavItem = {
      path: 'Settings/Data Definitions/Export Definitions',
      icon: 'data_definitions_icon_export_definition',
      widgetConfig: {
        name: 'Export Definitions',
        id: 'data-definitions-export',
        component: 'data-definitions-export',
        config: {
          icon: {
            type: 'name',
            value: 'data_definitions_icon_export_definition'
          }
        }
      }
    }
    mainNavRegistry.registerMainNavItem(exportNav)
  }
}
