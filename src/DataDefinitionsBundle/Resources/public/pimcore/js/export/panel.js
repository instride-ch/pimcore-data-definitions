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

pimcore.registerNS('pimcore.plugin.datadefinitions.export.panel');

pimcore.plugin.datadefinitions.export.panel = Class.create(coreshop.resource.panel, {
    layoutId: 'data_definitions_export_definition_panel',
    storeId: 'data_definitions_export_definitions',
    iconCls: 'data_definitions_icon_export_definition',
    type: 'definition',

    url: {
        add: '/admin/data_definitions/export_definitions/add',
        delete: '/admin/data_definitions/export_definitions/delete',
        get: '/admin/data_definitions/export_definitions/get'
    },

    routing: {
        add: null,
        delete: null,
        get: null
    },

    providers: [],
    cleaners: [],
    interpreters: [],
    setters: [],
    filters: [],
    runners: [],


    getTopBar: function () {
        return [
            {
                // add button
                text: t('add'),
                iconCls: 'pimcore_icon_add',
                itemId: 'add-button',
                handler: this.addItem.bind(this),
                disabled: !pimcore.settings['data-definitions-import-definition-writeable']
            }
        ];
    },

    getDefaultGridConfiguration: function () {
        return {
            region: 'west',
            store: pimcore.globalmanager.get(this.storeId),
            columns: [
                {
                    text: 'ID',
                    dataIndex: 'id',
                    flex: 1,
                    renderer: this.getGridDisplayColumnRenderer
                },
                {
                    text: 'Name',
                    dataIndex: this.getDefaultGridDisplayColumnName(),
                    flex: 4,
                    renderer: this.getGridDisplayColumnRenderer
                }

            ],
            listeners: this.getTreeNodeListeners(),
            useArrows: true,
            autoScroll: true,
            animate: true,
            containerScroll: true,
            width: 200,
            split: true,
            tbar: this.getTopBar(),
            bbar: {
                items: [{
                    xtype: 'label',
                    text: '',
                    itemId: 'totalLabel'
                }, '->', {
                    iconCls: 'pimcore_icon_reload',
                    scale: 'small',
                    handler: function () {
                        this.grid.getStore().load();
                    }.bind(this)
                }]
            },
            hideHeaders: false
        };
    },

    getTitle: function () {
        return t('data_definitions_export_definitions');
    },

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/data_definitions/export_definitions/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                this.providers = [];
                this.interpreters = [];
                this.runners = [];
                this.getters = [];
                this.fetchers = [];
                this.importRuleConditions = [];
                this.importRuleActions = [];

                config.providers.forEach(function (provider) {
                    this.providers.push([provider]);
                }.bind(this));

                config.interpreter.forEach(function (interpreter) {
                    this.interpreters.push([interpreter]);
                }.bind(this));

                config.runner.forEach(function (runner) {
                    this.runners.push([runner]);
                }.bind(this));

                config.getters.forEach(function (getter) {
                    this.getters.push([getter]);
                }.bind(this));

                config.fetchers.forEach(function (fetcher) {
                    this.fetchers.push([fetcher]);
                }.bind(this));

                var providerStore = new Ext.data.ArrayStore({
                    data: this.providers,
                    fields: ['provider'],
                    idProperty: 'provider'
                });

                pimcore.globalmanager.add('importdefinitions_export_providers', providerStore);
                pimcore.globalmanager.add('data_definitions_export_providers', providerStore);

                var interpretersStore = new Ext.data.ArrayStore({
                    data: this.interpreters,
                    fields: ['interpreter'],
                    idProperty: 'interpreter'
                });

                pimcore.globalmanager.add('importdefinitions_interpreters', interpretersStore);
                pimcore.globalmanager.add('data_definitions_interpreters', interpretersStore);

                var runnersStore = new Ext.data.ArrayStore({
                    data: this.runners,
                    fields: ['runner'],
                    idProperty: 'runner'
                });

                pimcore.globalmanager.add('importdefinitions_runners', runnersStore);
                pimcore.globalmanager.add('data_definitions_runners', runnersStore);

                var gettersStore = new Ext.data.ArrayStore({
                    data: this.getters,
                    fields: ['getter'],
                    idProperty: 'getter'
                });

                pimcore.globalmanager.add('importdefinitions_getters', gettersStore);
                pimcore.globalmanager.add('data_definitions_getters', gettersStore);

                var fetchersStore = new Ext.data.ArrayStore({
                    data: this.fetchers,
                    fields: ['fetcher'],
                    idProperty: 'fetcher'
                });

                pimcore.globalmanager.add('importdefinitions_fetchers', fetchersStore);
                pimcore.globalmanager.add('data_definitions_fetchers', fetchersStore);

                pimcore.globalmanager.add('data_definitions_import_rule_conditions', config.import_rules.conditions);
                pimcore.globalmanager.add('data_definitions_import_rule_actions', config.import_rules.actions);

                this.getLayout();
            }.bind(this)
        });

        this.panels = [];
    },

    getItemClass: function () {
        return pimcore.plugin.datadefinitions.export.item;
    }
});
