pimcore.registerNS("pimcore.plugin.advancedimportexport");

pimcore.plugin.advancedimportexport = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.advancedimportexport";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {

        var user = pimcore.globalmanager.get('user');

        if(user.isAllowed('plugins')) {

            var exportMenu = new Ext.Action({
                text: t('advancedimportexport_definitions'),
                iconCls: 'advancedimportexport_icon_definition',
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
            pimcore.globalmanager.get('advancedimportexport_definitions_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('advancedimportexport_definitions_panel', new pimcore.plugin.advancedimportexport.definition.panel());
        }
    }
});

var advancedimportexportPlugin = new pimcore.plugin.advancedimportexport();

