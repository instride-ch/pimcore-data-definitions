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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.iterator');

pimcore.plugin.datadefinitions.interpreters.iterator = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getStore: function () {
        return pimcore.globalmanager.get('data_definitions_interpreters');
    },

    getTitle: function () {
        return t('data_definitions_interpreter_settings');
    },

    getStoreIdentifier: function () {
        return 'interpreter';
    },

    getClassItem: function () {
        return pimcore.plugin.datadefinitions.interpreters;
    },

    getLayout: function (fromColumn, toColumn, record, config) {
        this.interpreterPanel = new Ext.form.FormPanel({
            defaults: {anchor: '90%'},
            layout: 'form',
            title: this.getTitle(),
            border: true,
            hidden: true
        });

        this.interpreterTypeCombo = new Ext.form.ComboBox({
            fieldLabel: t('data_definitions_interpreters'),
            name: this.getStoreIdentifier(),
            length: 255,
            value: config.interpreter ? config.interpreter.type : null,
            store: this.getStore(),
            valueField: this.getStoreIdentifier(),
            displayField: this.getStoreIdentifier(),
            queryMode: 'local',
            listeners: {
                change: function (combo, newValue) {
                    this.interpreterPanel.removeAll();

                    this.getInterpreterPanelLayout(newValue, fromColumn, toColumn, record, config, {});
                }.bind(this)
            }
        });

        this.interpreterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            items: [
                this.interpreterTypeCombo,
                this.interpreterPanel
            ],
            border: false
        });

        if (config && config.interpreter && config.interpreter.type) {
            this.getInterpreterPanelLayout(config.interpreter.type, fromColumn, toColumn, record, config, config.interpreter.interpreterConfig);
        }

        return this.interpreterContainer;
    },

    destroy: function () {
        if (this.interpreterContainer) {
            this.interpreterContainer.destroy();
        }
    },

    getInterpreterPanelLayout: function (type, fromColumn, toColumn, record, parentConfig, config) {
        if (type) {
            type = type.toLowerCase();

            var classItem = this.getClassItem();

            if (classItem[type]) {
                this.interpreter = new classItem[type];

                this.interpreterPanel.add(this.interpreter.getLayout(fromColumn, toColumn, record, Ext.isObject(config) ? config : {}, parentConfig));
                this.interpreterPanel.show();
            } else {
                this.interpreterPanel.hide();

                this.interpreter = null;
            }
        } else {
            this.interpreterPanel.hide();
        }
    },

    getInterpreterData: function () {
        // get defined conditions
        if (this.interpreter) {
            var interpreterConfig = {};
            var interpreterForm = this.interpreterPanel.getForm();

            if (Ext.isFunction(this.interpreter.getInterpreterData)) {
                interpreterConfig = this.interpreter.getInterpreterData();
            } else {
                Ext.Object.each(interpreterForm.getFieldValues(), function (key, value) {
                    interpreterConfig[key] = value;
                }.bind(this));
            }

            return {
                interpreter: {
                    interpreterConfig: interpreterConfig,
                    type: this.interpreterTypeCombo.getValue()
                }
            };
        }

        return {};
    }
});
