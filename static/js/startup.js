/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
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

            var exportMenu = new Ext.Action({
                text: t('importdefinitions_definitions'),
                iconCls: 'importdefinitions_icon_definition',
                handler:this.openDefinitions
            });

            /*var importMenu = new Ext.Action({
                text: t('importexport_import'),
                iconCls: 'pimcore_icon_import',
                handler:this.openImport
            });*/

            layoutToolbar.settingsMenu.add(exportMenu);

            //layoutToolbar.settingsMenu.add(importMenu);

            this.createGlobalStores();
        }
    },

    openDefinitions : function ()
    {
        try {
            pimcore.globalmanager.get('importdefinitions_definition_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('importdefinitions_definition_panel', new pimcore.plugin.importdefinitions.definition.panel());
        }
    },

    createGlobalStores : function() {
        var proxy = new Ext.data.HttpProxy({
            url : '/plugin/ImportDefinitions/admin_definition/list'
        });

        var reader = new Ext.data.JsonReader({}, [
            { name:'id' },
            { name:'name' }
        ]);

        var store = new Ext.data.Store({
            restful:    false,
            proxy:      proxy,
            reader:     reader,
            autoload:   true
        });

        pimcore.globalmanager.add('importdefinitions_definitions', store);
    }
});

var importdefinitionsPlugin = new pimcore.plugin.importdefinitions();

