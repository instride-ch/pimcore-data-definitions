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

pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.nested');

pimcore.plugin.importdefinitions.interpreters.nested = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        // init
        var _this = this;
        var addMenu = [];

        pimcore.globalmanager.get('importdefinitions_interpreters').clearFilter();

        var records = pimcore.globalmanager.get('importdefinitions_interpreters').getRange().map(function(interpreter) {return interpreter.get('interpreter')});

        Ext.each(records, function (interpreter) {
            if (interpreter === 'abstract')
                return;

            addMenu.push({
                text: interpreter,
                handler: _this.addInterpreter.bind(_this, interpreter, fromColumn, toColumn, record, {})
            });

        });

        this.interpreterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        if (config && config.interpreters) {
            Ext.each(config.interpreters, function (interpreter) {
                this.addInterpreter(interpreter.type, fromColumn, toColumn, record, interpreter.interpreterConfig);
            }.bind(this));
        }

        return this.interpreterContainer;
    },

    destroy: function () {
        if (this.interpreterContainer) {
            this.interpreterContainer.destroy();
        }
    },

    getInterpreterClassItem: function (type) {
        if (Object.keys(pimcore.plugin.importdefinitions.interpreters).indexOf(type) >= 0) {
            return pimcore.plugin.importdefinitions.interpreters[type];
        }

        return pimcore.plugin.importdefinitions.interpreters.empty;
    },

    addInterpreter: function (type, fromColumn, toColumn, record, config) {
        // create condition
        var interpreterClass = this.getInterpreterClassItem(type);
        var item = new interpreterClass();
        var container = new pimcore.plugin.importdefinitions.interpreters.nestedcontainer(this, type, item);

        this.interpreterContainer.add(container.getLayout(type, fromColumn, toColumn, record, config));
        this.interpreterContainer.updateLayout();
    },

    getInterpreterData: function () {
        // get defined conditions
        var interpreterData = [];
        var interpreters = this.interpreterContainer.items.getRange();
        for (var i = 0; i < interpreters.length; i++) {
            var configuration = {};
            var interpreter = {};

            var interpreterItem = interpreters[i];
            var interpreterClass = interpreterItem.xparent;

            if (Ext.isFunction(interpreterClass['getValues'])) {
                configuration = interpreterClass.getValues();
            } else {
                var form = interpreterClass.form;

                if (Ext.isFunction(form.getValues)) {
                    configuration = form.getValues();
                }
                else {
                    for (var c = 0; c < form.items.length; c++) {
                        var item = form.items.get(c);

                        try {
                            configuration[item.getName()] = item.getValue();
                        }
                        catch (e) {

                        }
                    }
                }
            }

            if (interpreterClass.data.id) {
                action['id'] = interpreterClass.data.id;
            }

            interpreter['interpreterConfig'] = configuration;
            interpreter['type'] = interpreters[i].xparent.type;

            interpreterData.push(interpreter);
        }

        return {
            interpreters: interpreterData
        };
    }
});
