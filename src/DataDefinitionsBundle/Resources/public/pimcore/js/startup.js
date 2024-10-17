/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

pimcore.registerNS('pimcore.plugin.data_definitions');

pimcore.plugin.data_definitions = Class.create({
    getClassName: function () {
        return 'pimcore.plugin.data_definitions';
    },

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, (e) => {
            this.pimcoreReady();
        });
    },

    pimcoreReady: function () {

        var user = pimcore.globalmanager.get('user');

        if (user.isAllowed('plugins')) {

            var importMenu = new Ext.Action({
                text: t('data_definitions_import_definitions'),
                iconCls: 'data_definitions_nav_icon_import_definition',
                handler: this.openImportDefinitions
            });

            layoutToolbar.settingsMenu.add(importMenu);

            var exportMenu = new Ext.Action({
                text: t('data_definitions_export_definitions'),
                iconCls: 'data_definitions_nav_icon_export_definition',
                handler: this.openExportDefinitions
            });

            layoutToolbar.settingsMenu.add(exportMenu);

            coreshop.global.addStore('data_definitions_definitions', 'data_definitions/import_definitions');
            coreshop.global.addStore('data_definitions_export_definitions', 'data_definitions/export_definitions');

            pimcore.globalmanager.add('importdefinitions_definitions', pimcore.globalmanager.get('data_definitions_definitions'));
            pimcore.globalmanager.add('importdefinitions_export_definitions', pimcore.globalmanager.get('data_definitions_export_definitions'));
        }
    },

    openImportDefinitions: function () {
        try {
            pimcore.globalmanager.get('data_definitions_import_definition_panel').activate();
        } catch (e) {
            pimcore.globalmanager.add('data_definitions_import_definition_panel', new pimcore.plugin.datadefinitions.import.panel());
        }
    },

    openExportDefinitions: function () {
        try {
            pimcore.globalmanager.get('data_definitions_export_definition_panel').activate();
        } catch (e) {
            pimcore.globalmanager.add('data_definitions_export_definition_panel', new pimcore.plugin.datadefinitions.export.panel());
        }
    }
});

new pimcore.plugin.data_definitions();

