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

pimcore.registerNS('pimcore.plugin.importdefinitions.definition.panel');

pimcore.plugin.importdefinitions.definition.panel = Class.create(coreshop.resource.panel, {
    layoutId: 'importdefinitions_definition_panel',
    storeId : 'importdefinitions_definitions',
    iconCls : 'importdefinitions_icon_definition',
    type : 'definition',

    url : {
        add : '/admin/import_definitions/definitions/add',
        delete : '/admin/import_definitions/definitions/delete',
        get : '/admin/import_definitions/definitions/get'
    },

    providers : [],
    cleaners : [],
    interpreters : [],
    setters : [],
    filters : [],
    runners : [],

    getTitle: function () {
        return t('importdefinitions_definitions');
    },

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/import_definitions/definitions/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                this.providers = [];
                this.filters = [];
                this.interpreters = [];
                this.setters = [];
                this.cleaners = [];
                this.runners = [];

                config.providers.forEach(function (provider) {
                    this.providers.push([provider]);
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

                var providerStore = new Ext.data.ArrayStore({
                    data : this.providers,
                    fields: ['provider'],
                    idProperty : 'provider'
                });

                pimcore.globalmanager.add('importdefinitions_providers', providerStore);

                var filterStore = new Ext.data.ArrayStore({
                    data : this.filters,
                    fields: ['filter'],
                    idProperty : 'filter'
                });

                pimcore.globalmanager.add('importdefinitions_filters', filterStore);

                var cleanersStore = new Ext.data.ArrayStore({
                    data : this.cleaners,
                    fields: ['cleaner'],
                    idProperty : 'cleaner'
                });

                pimcore.globalmanager.add('importdefinitions_cleaners', cleanersStore);

                var interpretersStore = new Ext.data.ArrayStore({
                    data : this.interpreters,
                    fields: ['interpreter'],
                    idProperty : 'interpreter'
                });

                pimcore.globalmanager.add('importdefinitions_interpreters', interpretersStore);

                var settersStore = new Ext.data.ArrayStore({
                    data : this.setters,
                    fields: ['setter'],
                    idProperty : 'setter'
                });

                pimcore.globalmanager.add('importdefinitions_setters', settersStore);

                var runnersStore = new Ext.data.ArrayStore({
                    data : this.runners,
                    fields: ['runner'],
                    idProperty : 'runner'
                });

                pimcore.globalmanager.add('importdefinitions_runners', runnersStore);

                this.getLayout();
            }.bind(this)
        });

        this.panels = [];
    },

    getItemClass: function () {
        return pimcore.plugin.importdefinitions.definition.item;
    }
});
