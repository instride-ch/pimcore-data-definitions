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

pimcore.registerNS('pimcore.plugin.datadefinitions.import.panel');

pimcore.plugin.datadefinitions.import.panel = Class.create(coreshop.resource.panel, {
    layoutId: 'data_definitions_import_definition_panel',
    storeId: 'data_definitions_definitions',
    iconCls: 'data_definitions_icon_import_definition',
    type: 'definition',

    url: {
        add: '/admin/data_definitions/import_definitions/add',
        delete: '/admin/data_definitions/import_definitions/delete',
        get: '/admin/data_definitions/import_definitions/get'
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
    persisters: [],

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
        return t('data_definitions_import_definitions');
    },

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/data_definitions/import_definitions/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                this.providers = [];
                this.loaders = [];
                this.filters = [];
                this.interpreters = [];
                this.setters = [];
                this.cleaners = [];
                this.runners = [];
                this.persisters = [];

                config.providers.forEach(function (provider) {
                    this.providers.push([provider]);
                }.bind(this));

                config.loaders.forEach(function (loader) {
                    this.loaders.push([loader]);
                }.bind(this));

                config.filters.forEach(function (filter) {
                    this.filters.push([filter]);
                }.bind(this));

                config.interpreter.forEach(function (interpreter) {
                    this.interpreters.push([interpreter]);
                }.bind(this));

                config.setter.forEach(function (setter) {
                    this.setters.push([setter]);
                }.bind(this));

                config.cleaner.forEach(function (cleaner) {
                    this.cleaners.push([cleaner]);
                }.bind(this));

                config.runner.forEach(function (runner) {
                    this.runners.push([runner]);
                }.bind(this));

                config.persister.forEach(function (persister) {
                    this.persisters.push([persister]);
                }.bind(this));

                var providerStore = new Ext.data.ArrayStore({
                    data: this.providers,
                    fields: ['provider'],
                    idProperty: 'provider'
                });

                pimcore.globalmanager.add('importdefinitions_providers', providerStore);
                pimcore.globalmanager.add('data_definitions_providers', providerStore);

                var loaderStore = new Ext.data.ArrayStore({
                    data: this.loaders,
                    fields: ['loader'],
                    idProperty: 'loader'
                });

                pimcore.globalmanager.add('importdefinitions_loaders', loaderStore);
                pimcore.globalmanager.add('data_definitions_loaders', loaderStore);

                var filterStore = new Ext.data.ArrayStore({
                    data: this.filters,
                    fields: ['filter'],
                    idProperty: 'filter'
                });

                pimcore.globalmanager.add('importdefinitions_filters', filterStore);
                pimcore.globalmanager.add('data_definitions_filters', filterStore);

                var cleanersStore = new Ext.data.ArrayStore({
                    data: this.cleaners,
                    fields: ['cleaner'],
                    idProperty: 'cleaner'
                });

                pimcore.globalmanager.add('importdefinitions_cleaners', cleanersStore);
                pimcore.globalmanager.add('data_definitions_cleaners', cleanersStore);

                var interpretersStore = new Ext.data.ArrayStore({
                    data: this.interpreters,
                    fields: ['interpreter'],
                    idProperty: 'interpreter'
                });

                pimcore.globalmanager.add('importdefinitions_interpreters', interpretersStore);
                pimcore.globalmanager.add('data_definitions_interpreters', interpretersStore);

                var settersStore = new Ext.data.ArrayStore({
                    data: this.setters,
                    fields: ['setter'],
                    idProperty: 'setter'
                });

                pimcore.globalmanager.add('importdefinitions_setters', settersStore);
                pimcore.globalmanager.add('data_definitions_setters', settersStore);

                var runnersStore = new Ext.data.ArrayStore({
                    data: this.runners,
                    fields: ['runner'],
                    idProperty: 'runner'
                });

                pimcore.globalmanager.add('importdefinitions_runners', runnersStore);
                pimcore.globalmanager.add('data_definitions_runners', runnersStore);

                var persistersStore = new Ext.data.ArrayStore({
                    data: this.persisters,
                    fields: ['persister'],
                    idProperty: 'persister'
                });

                pimcore.globalmanager.add('importdefinitions_persisters', persistersStore);
                pimcore.globalmanager.add('data_definitions_persisters', persistersStore);

                pimcore.globalmanager.add('data_definitions_import_rule_conditions', config.import_rules.conditions);
                pimcore.globalmanager.add('data_definitions_import_rule_actions', config.import_rules.actions);

                this.getLayout();
            }.bind(this)
        });

        this.panels = [];
    },

    getItemClass: function () {
        return pimcore.plugin.datadefinitions.import.item;
    }
});
