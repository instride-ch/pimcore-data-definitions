/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions');

pimcore.plugin.importdefinitions = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return 'pimcore.plugin.importdefinitions';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {

        var user = pimcore.globalmanager.get('user');

        if (user.isAllowed('plugins')) {

            var importMenu = new Ext.Action({
                text: t('importdefinitions_import_definitions'),
                iconCls: 'importdefinitions_icon_import_definition',
                handler:this.openImportDefinitions
            });

            layoutToolbar.settingsMenu.add(importMenu);

            var exportMenu = new Ext.Action({
                text: t('importdefinitions_export_definitions'),
                iconCls: 'importdefinitions_icon_export_definition',
                handler:this.openExportDefinitions
            });

            layoutToolbar.settingsMenu.add(exportMenu);

            coreshop.global.addStore('importdefinitions_definitions', 'import_definitions/definitions');
            coreshop.global.addStore('importdefinitions_export_definitions', 'import_definitions/export_definitions');
        }
    },

    openImportDefinitions : function ()
    {
        try {
            pimcore.globalmanager.get('importdefinitions_import_definition_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('importdefinitions_import_definition_panel', new pimcore.plugin.importdefinitions.import.panel());
        }
    },

    openExportDefinitions : function ()
    {
        try {
            pimcore.globalmanager.get('importdefinitions_export_definition_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('importdefinitions_export_definition_panel', new pimcore.plugin.importdefinitions.export.panel());
        }
    }
});

var importdefinitionsPlugin = new pimcore.plugin.importdefinitions();

