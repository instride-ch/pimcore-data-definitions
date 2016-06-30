pimcore.registerNS('pimcore.plugin.importdefinitions.definition.panel');

pimcore.plugin.importdefinitions.definition.panel = Class.create({
    layoutId: 'importdefinitions_definition_panel',
    storeId : 'importdefinitions_definitions',
    iconCls : 'importdefinitions_icon_definition',
    type : 'definition',

    url : {
        add : '/plugin/ImportDefinitions/admin_definition/add',
        delete : '/plugin/ImportDefinitions/admin_definition/delete',
        get : '/plugin/ImportDefinitions/admin_definition/get',
        list : '/plugin/ImportDefinitions/admin_definition/list'
    },

    providers : [],
    cleaners : [],
    interpreters : [],

    initialize: function () {
        // create layout
        this.createStore();

        Ext.Ajax.request({
            url: '/plugin/ImportDefinitions/admin_definition/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                this.providers = [];
                this.cleaners = [];
                this.interpreters = [];

                config.providers.forEach(function(provider) {
                    this.providers.push([provider]);
                }.bind(this));

                config.interpreter.forEach(function(interpreter) {
                    this.interpreters.push([interpreter]);
                }.bind(this));

                config.cleaner.forEach(function(cleaner) {
                    this.cleaners.push([cleaner]);
                }.bind(this));

                var providerStore = new Ext.data.ArrayStore({
                    data : this.providers,
                    fields: ["provider"],
                    idProperty : "provider"
                });

                pimcore.globalmanager.add("importdefinitions_providers", providerStore);

                var cleanersStore = new Ext.data.ArrayStore({
                    data : this.cleaners,
                    fields: ["cleaner"],
                    idProperty : "cleaner"
                });

                pimcore.globalmanager.add("importdefinitions_cleaners", cleanersStore);

                var interpretersStore = new Ext.data.ArrayStore({
                    data : this.interpreters,
                    fields: ["interpreter"],
                    idProperty : "interpreter"
                });

                pimcore.globalmanager.add("importdefinitions_interpreters", interpretersStore);

                this.getLayout();
            }.bind(this)
        });

        this.panels = [];
    },

    createStore : function() {
        var proxy = new Ext.data.HttpProxy({
            url : this.url.list
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

        pimcore.globalmanager.add(this.storeId, store);
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: this.layoutId,
                title: t('importdefinitions_definitions'),
                iconCls: this.iconCls,
                border: false,
                layout: 'border',
                closable: true,
                items: this.getItems()
            });

            // add event listener
            var layoutId = this.layoutId;
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove(layoutId);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    refresh : function () {
        if (pimcore.globalmanager.exists(this.storeId)) {
            pimcore.globalmanager.get(this.storeId).load();
        }
    },

    getItems : function () {
        return [this.getNavigation(), this.getTabPanel()];
    },

    getNavigation: function () {
        if (!this.grid) {

            this.grid = Ext.create('Ext.grid.Panel', {
                region: 'west',
                store: pimcore.globalmanager.get(this.storeId),
                columns: [
                    {
                        text: '',
                        dataIndex: 'text',
                        flex : 1,
                        renderer: function (value, metadata, record)
                        {
                            metadata.tdCls = record.get('iconCls') + ' td-icon';

                            return value;
                        }
                    }
                ],
                listeners : this.getTreeNodeListeners(),
                useArrows: true,
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 200,
                split: true,
                tbar: {
                    items: [
                        {
                            // add button
                            text: t('add'),
                            iconCls: 'pimcore_icon_add',
                            handler: this.addItem.bind(this)
                        }
                    ]
                },
                hideHeaders: true
            });

            this.grid.on('beforerender', function () {
                this.getStore().load();
            });

        }

        return this.grid;
    },

    getTreeNodeListeners: function () {

        return {
            itemclick : this.onTreeNodeClick.bind(this),
            itemcontextmenu: this.onTreeNodeContextmenu.bind(this)
        };
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: 'pimcore_icon_delete',
            handler: this.deleteItem.bind(this, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts) {
        this.openItem(record.data);
    },

    addItem: function () {
        Ext.MessageBox.prompt(t('add'), t('name'),
            this.addItemComplete.bind(this), null, null, '');
    },

    addItemComplete: function (button, value, object) {
        if (button == 'ok' && value.length > 2) {
            Ext.Ajax.request({
                url: this.url.add,
                params: {
                    name: value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.grid.getStore().reload();

                    this.refresh();

                    if (!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openItem(data.data);
                    }
                }.bind(this)
            });
        } else if (button == 'cancel') {
            return;
        } else {
            Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
        }
    },

    deleteItem: function (record) {
        Ext.Ajax.request({
            url: this.url.delete,
            params: {
                id: record.id
            },
            success: function () {
                this.grid.getStore().reload();
                this.refresh();

                if (this.panels[this.getPanelKey(record)]) {
                    this.panels[this.getPanelKey(record)].destroy();
                }

            }.bind(this)
        });
    },

    getPanelKey : function (record) {
        return this.layoutId + record.id;
    },

    openItem: function (record) {
        var panelKey = this.getPanelKey(record);

        if (this.panels[panelKey])
        {
            this.panels[panelKey].activate();
        } else
        {
            Ext.Ajax.request({
                url: this.url.get,
                params: {
                    id: record.id
                },
                success: function (response) {
                    var res = Ext.decode(response.responseText);

                    if (res.success) {
                        var itemClass = this.getItemClass();

                        this.panels[panelKey] = new itemClass(this, res.data, panelKey, this.type, this.storeId);
                    } else {
                        //TODO: Show messagebox
                        Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                    }

                }.bind(this)
            });
        }
    },

    getItemClass : function () {
        return pimcore.plugin.importdefinitions[this.type].item;
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.TabPanel({
                region: 'center',
                border: false
            });
        }

        return this.panel;
    }
});