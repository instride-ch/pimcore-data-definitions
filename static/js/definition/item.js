pimcore.registerNS('pimcore.plugin.advancedimportexport.definition.item');

pimcore.plugin.advancedimportexport.definition.item = Class.create({

    iconCls : 'advancedimportexport_icon_definition',
    url : {
        save : '/plugin/AdvancedImportExport/admin_definition/save'
    },

    providers : [],

    initialize: function (parentPanel, data, panelKey, type) {
        this.parentPanel = parentPanel;
        this.data = data;
        this.panelKey = panelKey;
        this.type = type;

        this.initPanel();
    },

    initPanel: function () {
        this.panel = this.getPanel();

        this.panel.on('beforedestroy', function () {
            delete this.parentPanel.panels[this.panelKey];
        }.bind(this));

        this.parentPanel.getTabPanel().add(this.panel);
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    destroy : function () {
        if (this.panel) {
            this.panel.destroy();
        }
    },

    activate : function () {
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    getPanel: function () {
        var panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });

        return panel;
    },

    getItems : function () {
        return [
            this.getSettings(),
            this.getProviderSettings(),
            this.getMappingSettings()
        ];
    },

    getSettings : function () {

        var classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/class/get-tree'
            },
            fields: ["text"]
        });
        classesStore.load();

        this.configForm = new Ext.form.Panel({
            bodyStyle: 'padding:10px;',
            title : t('settings'),
            autoScroll: true,
            defaults : {
                labelWidth : 200
            },
            border:false,
            items: [
                {
                    xtype : 'combo',
                    fieldLabel: t("advancedimportexport_provider"),
                    name: "provider",
                    displayField: "provider",
                    valueField: "provider",
                    store: pimcore.globalmanager.get("advancedimportexport_providers"),
                    value : this.data.provider,
                    width: 500,
                    listeners : {
                        change : function (combo, value) {
                            this.data.provider = value;
                            this.reloadProviderSettings();
                            this.reloadColumnMapping();
                        }.bind(this)
                    }
                },
                {
                    xtype : 'combo',
                    fieldLabel: t("class"),
                    name: "class",
                    displayField: "text",
                    valueField: "text",
                    store: classesStore,
                    width: 500,
                    value : this.data.class
                }
            ]
        });

        return this.configForm;
    },

    getProviderSettings : function() {
        if(!this.providerSettings) {
            this.providerSettings = Ext.create({
                xtype : 'panel',
                layout : 'border',
                title : t('advancedimportexport_provider_settings'),
                disabled : true
            });
        }

        if(this.data.provider) {
            this.reloadProviderSettings();
        }

        return this.providerSettings;
    },

    reloadProviderSettings : function() {
        if(this.providerSettings) {
            this.providerSettings.removeAll();

            if(pimcore.plugin.advancedimportexport.provider[this.data.provider] !== undefined) {
                this.providerSettings.add(new pimcore.plugin.advancedimportexport.provider[this.data.provider](this.data.providerConfiguration).getForm());
                this.providerSettings.enable();
            }
        }
    },

    getMappingSettings : function() {
        if(!this.mappingSettings) {
            this.mappingSettings = Ext.create({
                xtype : 'panel',
                layout : 'border',
                title : t('advancedimportexport_mapping_settings'),
                disabled : true
            });
        }

        if(this.data.provider) {
            this.reloadColumnMapping();
        }

        return this.mappingSettings;
    },

    reloadColumnMapping : function() {
        if(this.mappingSettings) {
            this.mappingSettings.removeAll();

            if(this.data.provider) {
                this.mappingSettings.enable();

                var gridStore = new Ext.data.Store({
                    fields : [
                        'fromColumn',
                        'toColumn'
                    ]
                });

                var fromColumnStore = new Ext.data.Store({
                    fields : [
                        'identifier',
                        'label'
                    ],
                    idProperty : 'identifier'
                });

                var pickerStore = new Ext.data.TreeStore({

                });

                Ext.Ajax.request({
                    url: '/plugin/AdvancedImportExport/admin_definition/get-columns',
                    params : {
                        id : this.data.id
                    },
                    method: 'GET',
                    success: function (result) {
                        var config = Ext.decode(result.responseText);
                        var gridStoreData = [];

                        config.fromColumns.forEach(function(col) {
                            gridStoreData.push({
                                fromColumn : col
                            });
                        });

                        gridStore.loadRawData(gridStoreData);
                        pickerStore.loadRawData(config.toColumns);

                        var cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing');

                        var grid = Ext.create({
                            xtype : 'grid',
                            region : 'center',
                            store : gridStore,
                            plugins : [cellEditingPlugin],
                            columns : {
                                defaults : {},
                                items : [
                                    {
                                        text : t('advancedimportexport_fromColumn'),
                                        dataIndex : 'fromColumn',
                                        flex : 1,
                                        editor : {
                                            xtype : 'combo',
                                            store : fromColumnStore
                                        },
                                        renderer : function(val) {
                                            return val.label;
                                        }
                                    },
                                    {
                                        text : t('advancedimportexport_toColumn'),
                                        dataIndex : 'toColumn',
                                        flex : 1,
                                        editor : {
                                            xtype : 'classTreePicker',
                                            data : config.toColumns
                                        },
                                        renderer : function(val) {
                                            if(val && Ext.isObject(val)) {
                                                return val.objectType + (val.className ? "~" + val.className : "") + "~" + val.name;
                                            }

                                            return val;
                                        }
                                    }
                                ]
                            }

                        });

                        this.mappingSettings.add(grid);
                    }.bind(this)
                });
            }
        }
    },

    getSaveData : function () {
        var data = {
            configuration: {}
        };

        Ext.apply(data, this.configForm.getForm().getFieldValues());

        if(this.providerSettings.down("form")) {
            Ext.apply(data.configuration, this.providerSettings.down("form").getForm().getFieldValues());
        }

        return {
            data : Ext.encode(data)
        };
    },

    save: function ()
    {
        var saveData = this.getSaveData();

        saveData['id'] = this.data.id;

        Ext.Ajax.request({
            url: this.url.save,
            method: 'post',
            params: saveData,
            success: function (response) {
                try {
                    if (this.parentPanel.store) {
                        this.parentPanel.store.load();
                    }

                    if (res.success) {
                        pimcore.helpers.showNotification(t('success'), t('success'), 'success');

                        this.data = res.data;

                        this.panel.setTitle(this.getTitleText());
                    } else {
                        pimcore.helpers.showNotification(t('error'), t('error'),
                            'error', res.message);
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t('error'), t('error'), 'error');
                }
            }.bind(this)
        });
    }
});