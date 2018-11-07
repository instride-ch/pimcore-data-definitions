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

pimcore.registerNS('pimcore.plugin.importdefinitions.export.panel');

pimcore.plugin.importdefinitions.export.panel = Class.create(coreshop.resource.panel, {
    layoutId: 'importdefinitions_export_definition_panel',
    storeId : 'importdefinitions_export_definitions',
    iconCls : 'importdefinitions_icon_export_definition',
    type : 'definition',

    url : {
        add : '/admin/import_definitions/export_definitions/add',
        delete : '/admin/import_definitions/export_definitions/delete',
        get : '/admin/import_definitions/export_definitions/get'
    },

    providers : [],
    cleaners : [],
    interpreters : [],
    setters : [],
    filters : [],
    runners : [],

    getTitle: function () {
        return t('importdefinitions_export_definitions');
    },

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/import_definitions/export_definitions/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                this.providers = [];
                this.interpreters = [];
                this.runners = [];
                this.getters = [];
                this.fetchers = [];

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
                    data : this.providers,
                    fields: ['provider'],
                    idProperty : 'provider'
                });

                pimcore.globalmanager.add('importdefinitions_providers', providerStore);

                var interpretersStore = new Ext.data.ArrayStore({
                    data : this.interpreters,
                    fields: ['interpreter'],
                    idProperty : 'interpreter'
                });

                pimcore.globalmanager.add('importdefinitions_interpreters', interpretersStore);

                var runnersStore = new Ext.data.ArrayStore({
                    data : this.runners,
                    fields: ['runner'],
                    idProperty : 'runner'
                });

                pimcore.globalmanager.add('importdefinitions_runners', runnersStore);

                var gettersStore = new Ext.data.ArrayStore({
                    data : this.getters,
                    fields: ['getter'],
                    idProperty : 'getter'
                });

                pimcore.globalmanager.add('importdefinitions_getters', gettersStore);

                var fetchersStore = new Ext.data.ArrayStore({
                    data : this.fetchers,
                    fields: ['fetcher'],
                    idProperty : 'fetcher'
                });

                pimcore.globalmanager.add('importdefinitions_fetchers', fetchersStore);

                this.getLayout();
            }.bind(this)
        });

        this.panels = [];
    },

    getItemClass: function () {
        return pimcore.plugin.importdefinitions.export.item;
    }
});
