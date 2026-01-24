/**
 * Data Definitions - Icon Library Module
 *
 * Registers custom icons for Data Definitions in the Pimcore Studio icon library
 */

import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'
import React from 'react'

// Import Definition Icon SVG
const ImportDefinitionIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" width="100%" height="100%">
    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11zm-9-6h4v4h2v-4h4v-2h-4v-4h-2v4H9v2z"/>
  </svg>
)

// Export Definition Icon SVG
const ExportDefinitionIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" width="100%" height="100%">
    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11zm-9-6h4v-4h2v4h4v2h-4v4h-2v-4H9v-2z"/>
  </svg>
)

// Data Definitions Logo Icon
const DataDefinitionsIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" width="100%" height="100%">
    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
  </svg>
)

export const DataDefinitionsIconModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Register Data Definitions icons
    iconLibrary.register({
      name: 'data_definitions_icon',
      component: DataDefinitionsIcon
    })

    iconLibrary.register({
      name: 'data_definitions_icon_import_definition',
      component: ImportDefinitionIcon
    })

    iconLibrary.register({
      name: 'data_definitions_icon_export_definition',
      component: ExportDefinitionIcon
    })
  }
}
