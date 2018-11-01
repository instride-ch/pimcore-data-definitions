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

pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.conditional');

pimcore.plugin.importdefinitions.interpreters.conditional = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        this.condition = Ext.create({
            xtype : 'textfield',
            fieldLabel: t('importdefinitions_interpreter_conditional_condition'),
            name: 'expression',
            width: 500,
            value : config.condition ? config.condition : null
        });

        this.trueInterpreterPanel = new Ext.form.FormPanel({
            layout: 'form',
            title : t('importdefinitions_interpreter_conditional_settings_true'),
            border: true,
            hidden: true
        });

        this.trueInterpreterTypeCombo = new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_interpreter_conditional_true'),
            name : 'interpreter',
            length : 255,
            value : config.true_interpreter ? config.true_interpreter .type : null,
            store : pimcore.globalmanager.get('importdefinitions_interpreters'),
            valueField : 'interpreter',
            displayField : 'interpreter',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.trueInterpreterPanel.removeAll();

                    this.getTrueInterpreterPanelLayout(newValue, fromColumn, toColumn, record, config, {});
                }.bind(this)
            }
        });

        this.trueInterpreterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            items: [
                this.trueInterpreterTypeCombo,
                this.trueInterpreterPanel
            ],
            border: false
        });

        this.falseInterpreterPanel = new Ext.form.FormPanel({
            layout: 'form',
            title : t('importdefinitions_interpreter_conditional_settings_false'),
            border: true,
            hidden: true
        });

        this.falseInterpreterTypeCombo = new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_interpreter_conditional_false'),
            name : 'interpreter',
            length : 255,
            value : config.false_interpreter ? config.false_interpreter.type : null,
            store : pimcore.globalmanager.get('importdefinitions_interpreters'),
            valueField : 'interpreter',
            displayField : 'interpreter',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.falseInterpreterPanel.removeAll();

                    this.getFalseInterpreterPanelLayout(newValue, fromColumn, toColumn, record, config, {});
                }.bind(this)
            }
        });

        this.falseInterpreterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            items: [
                this.falseInterpreterTypeCombo,
                this.falseInterpreterPanel
            ],
            border: false
        });

        if (config && config.false_interpreter && config.false_interpreter.type) {
            this.getFalseInterpreterPanelLayout(config.false_interpreter.type, fromColumn, toColumn, record, config, config.false_interpreter.interpreterConfig);
        }

        if (config && config.true_interpreter && config.true_interpreter.type) {
            this.getTrueInterpreterPanelLayout(config.true_interpreter.type, fromColumn, toColumn, record, config, config.true_interpreter.interpreterConfig);
        }

        return new Ext.Panel({
            items: [
                this.condition,
                this.trueInterpreterContainer,
                this.falseInterpreterContainer
            ]
        });
    },

    destroy: function () {
        if (this.trueInterpreterContainer) {
            this.trueInterpreterContainer.destroy();
        }

        if (this.falseInterpreterContainer) {
            this.falseInterpreterContainer.destroy();
        }
    },

    getTrueInterpreterPanelLayout : function (type, fromColumn, toColumn, record, parentConfig, config) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.importdefinitions.interpreters[type]) {
                this.trueInterpreter = new pimcore.plugin.importdefinitions.interpreters[type];

                this.trueInterpreterPanel.add(this.trueInterpreter.getLayout(fromColumn, toColumn, record, Ext.isObject(config) ? config : {}, parentConfig));
                this.trueInterpreterPanel.show();
            } else {
                this.trueInterpreterPanel.hide();

                this.trueInterpreter = null;
            }
        } else {
            this.trueInterpreterPanel.hide();
        }
    },

    getFalseInterpreterPanelLayout : function (type, fromColumn, toColumn, record, parentConfig, config) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.importdefinitions.interpreters[type]) {
                this.falseInterpreter = new pimcore.plugin.importdefinitions.interpreters[type];

                this.falseInterpreterPanel.add(this.falseInterpreter.getLayout(fromColumn, toColumn, record, Ext.isObject(config) ? config : {}, parentConfig));
                this.falseInterpreterPanel.show();
            } else {
                this.falseInterpreterPanel.hide();

                this.falseInterpreter = null;
            }
        } else {
            this.falseInterpreterPanel.hide();
        }
    },

    getInterpreterData: function () {
        var trueInterpreterConfig  = {};
        var trueInterpreterForm = this.trueInterpreterPanel.getForm();

        var falseInterpreterConfig  = {};
        var falseInterpreterForm = this.falseInterpreterPanel.getForm();

        if (Ext.isFunction(this.trueInterpreter.getInterpreterData)) {
            trueInterpreterConfig = this.trueInterpreter.getInterpreterData();
        }
        else {
            Ext.Object.each(trueInterpreterForm.getFieldValues(), function (key, value) {
                trueInterpreterConfig[key] = value;
            }.bind(this));
        }

        if (Ext.isFunction(this.falseInterpreter.getInterpreterData)) {
            falseInterpreterConfig = this.falseInterpreter.getInterpreterData();
        }
        else {
            Ext.Object.each(falseInterpreterForm.getFieldValues(), function (key, value) {
                falseInterpreterConfig[key] = value;
            }.bind(this));
        }

        return {
            condition: this.condition.getValue(),
            true_interpreter: {
                interpreterConfig: trueInterpreterConfig,
                type: this.trueInterpreterTypeCombo.getValue()
            },
            false_interpreter: {
                interpreterConfig: falseInterpreterConfig,
                type: this.falseInterpreterTypeCombo.getValue()
            }
        };
    }
});
