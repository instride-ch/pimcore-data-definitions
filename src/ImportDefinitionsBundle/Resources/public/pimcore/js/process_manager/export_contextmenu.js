pimcore.registerNS("pimcore.plugin.importdefinitions.export.context_menu");

pimcore.plugin.importdefinitions.export.context_menu = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.importdefinitions.export.context_menu";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    prepareObjectTreeContextMenu: function (tree, treeClass, menuItem) {
        // TODO: replace with data store
        var objectTypes = [
            {id: 4, label: 'Cat'}
        ];

        var $this = this,
            exportMenu = [];

        objectTypes.forEach(function (executable) {
            exportMenu.push({
                text: executable.label,
                iconCls: "pimcore_icon_object pimcore_icon_overlay_add",
                handler: $this.exportObjects.bind($this, executable, menuItem)
            });
        });

        if (objectTypes) {
            tree.add([
                { xtype: 'menuseparator' },
                {
                    text: t("importdefinitions_processmanager_export_from_here"),
                    iconCls: "pimcore_icon_object pimcore_icon_overlay_download",
                    menu: exportMenu
                }
            ]);
        }
    },

    exportObjects: function (executable, menuItem) {
        Ext.Ajax.request({
            url: '/admin/process_manager/executables/run',
            params: {
                id: executable.id,
                root: menuItem.get('id')
            },
            method: 'POST',
            success: function (result) {
                result = Ext.decode(result.responseText);

                if (result.success) {
                    Ext.Msg.alert(t('success'), t('processmanager_executable_started'));
                } else {
                    Ext.Msg.alert(t('error'), result.message);
                }
            }.bind(this)
        });
    }
});

new pimcore.plugin.importdefinitions.export.context_menu();
