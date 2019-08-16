pimcore.registerNS("pimcore.plugin.datadefinitions.export.context_menu");

pimcore.plugin.datadefinitions.export.context_menu = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.datadefinitions.export.context_menu";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    prepareObjectTreeContextMenu: function (tree, treeClass, menuItem) {
        if (!Ext.ClassManager.get('Executable')) {
            Ext.define('Executable', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'name', type: 'string'},
                ]
            });
        }

        var $this = this;
        Ext.create('Ext.data.Store', {
            model: 'Executable',
            proxy: {
                type: 'ajax',
                url: '/admin/process_manager/executables/list-by-type',
                extraParams: {
                    type: 'exportdefinition'
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            sorters: [{
                property: 'name',
                direction: 'ASC'
            }],
            sortRoot: 'data',
            autoLoad: true,
            listeners: {
                refresh: function(store) {
                    var exportMenu = [];
                    store.each(function (executable) {
                        exportMenu.push({
                            text: executable.get('name'),
                            iconCls: "pimcore_icon_object pimcore_icon_overlay_add",
                            handler: $this.exportObjects.bind($this, executable, menuItem)
                        });
                    });

                    if (exportMenu) {
                        tree.add([
                            { xtype: 'menuseparator' },
                            {
                                text: t("data_definitions_processmanager_export_from_here"),
                                iconCls: "pimcore_icon_object pimcore_icon_overlay_download",
                                menu: exportMenu
                            }
                        ]);
                    }
                }
            }
        });
    },

    exportObjects: function (executable, menuItem) {
        Ext.Ajax.request({
            url: '/admin/process_manager/executables/run',
            params: {
                id: executable.id,
                startupConfig: Ext.encode({
                    root: menuItem.get('id'),
                }),
                csrfToken: pimcore.settings['csrfToken']
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

new pimcore.plugin.datadefinitions.export.context_menu();
