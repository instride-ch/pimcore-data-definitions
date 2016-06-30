pimcore.registerNS("pimcore.plugin.importdefinitions");

pimcore.plugin.importdefinitions = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.importdefinitions";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {

        var user = pimcore.globalmanager.get('user');

        if(user.isAllowed('plugins')) {

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

        }
    },

    openDefinitions : function()
    {
        try {
            pimcore.globalmanager.get('importdefinitions_definitions_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('importdefinitions_definitions_panel', new pimcore.plugin.importdefinitions.definition.panel());
        }
    }
});

var importdefinitionsPlugin = new pimcore.plugin.importdefinitions();

