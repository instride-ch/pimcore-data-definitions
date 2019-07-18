/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.filters.nested');

pimcore.plugin.datadefinitions.filters.nested = Class.create(pimcore.plugin.datadefinitions.filters.abstract, {
    getStore: function() {
        return pimcore.globalmanager.get('data_definitions_filters');
    },

    getClassItem: function() {
        return pimcore.plugin.datadefinitions.filters;
    },

    getFilterIdentifier: function(filter) {
        return filter.get('filter');
    },

    getLayout: function (config) {
        // init
        var _this = this;
        var addMenu = [];

        this.getStore().clearFilter();

        var records = this.getStore().getRange().map(function(filter) {return _this.getFilterIdentifier(filter);});

        Ext.each(records, function (filter) {
            if (filter === 'abstract')
                return;

            addMenu.push({
                text: filter,
                handler: _this.addFilter.bind(_this, filter, {})
            });

        });

        this.filterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        if (config && config.filters) {
            Ext.each(config.filters, function (filter) {
                this.addFilter(filter.type, filter.filterConfig);
            }.bind(this));
        }

        return this.filterContainer;
    },

    destroy: function () {
        if (this.filterContainer) {
            this.filterContainer.destroy();
        }
    },

    getFilterClassItem: function (type) {
        var items = this.getClassItem();

        if (Object.keys(items).indexOf(type) >= 0) {
            return items[type];
        }

        return items.empty;
    },

    addFilter: function (type, config) {
        var filterClass = this.getFilterClassItem(type);
        var item = new filterClass();
        var container = new pimcore.plugin.datadefinitions.filters.nestedcontainer(this, type, item);

        this.filterContainer.add(container.getLayout(type, fromColumn, toColumn, record, config));
        this.filterContainer.updateLayout();
    },

    getFilterData: function () {
        // get defined conditions
        var filterData = [];
        var filters = this.filterContainer.items.getRange();
        for (var i = 0; i < filters.length; i++) {
            var configuration = {};
            var filter = {};

            var filterItem = filters[i];
            var filterClass = filterItem.xparent;


            if (Ext.isFunction(filterClass['getValues'])) {
                configuration = filterClass.getValues();
            }

            filter['filterConfig'] = configuration;
            filter['type'] = filters[i].xparent.type;

            filterData.push(filter);
        }

        return {
            filters: filterData
        };
    }
});
