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

pimcore.registerNS('pimcore.plugin.importdefinitions.definition.configDialog');

pimcore.plugin.importdefinitions.definition.configDialog = Class.create({
    getConfigDialog : function (fromColumn, toColumn, record, config) {
        var fieldSetItems = [];

        this.fromColumn = fromColumn;
        this.toColumn = toColumn;
        this.record = record;
        this.config = config;

        fieldSetItems.push(new Ext.form.TextField({
            fieldLabel : t('importdefinitions_fromColumn'),
            name : 'fromColumn',
            length : 255,
            value : fromColumn.data.label,
            disabled : true
        }));

        fieldSetItems.push(new Ext.form.TextField({
            fieldLabel : t('importdefinitions_toColumn'),
            name : 'fromColumn',
            length : 255,
            value : toColumn.data.label,
            disabled : true
        }));

        if (!Ext.isObject(record.data.config)) {
            record.data.config = {};
        }

        if (!record.data.setter) {
            if (toColumn.data.type === 'objectbrick') {
                record.data.setter = 'objectbrick';
            } else if (toColumn.data.type === 'classificationstore') {
                record.data.setter = 'classificationstore';
            } else if (toColumn.data.type === 'fieldcollection') {
                record.data.setter = 'fieldcollection';
            }
        }

        if (!record.data.getter) {
            if (toColumn.data.type === 'objectbrick') {
                record.data.getter = 'objectbrick';
            } else if (toColumn.data.type === 'classificationstore') {
                record.data.getter = 'classificationstore';
            } else if (toColumn.data.type === 'fieldcollection') {
                record.data.getter = 'fieldcollection';
            }
        }

        if (!record.data.interpreter) {
            if (toColumn.data.fieldtype === 'quantityValue') {
                record.data.interpreter = 'quantity_value';
            }
        }

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_interpreters'),
            name : 'interpreter',
            length : 255,
            value : record.data.interpreter,
            store : pimcore.globalmanager.get('importdefinitions_interpreters'),
            valueField : 'interpreter',
            displayField : 'interpreter',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.getInterpreterPanel().removeAll();

                    this.getInterpreterPanelLayout(newValue);
                }.bind(this)
            }
        }));

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_reverse_interpreters'),
            name : 'reverseInterpreter',
            length : 255,
            value : record.data.reverseInterpreter,
            store : pimcore.globalmanager.get('importdefinitions_reverse_interpreters'),
            valueField : 'reverseInterpreter',
            displayField : 'reverseInterpreter',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.getReverseInterpreterPanel().removeAll();

                    this.getReverseInterpreterPanelLayout(newValue);
                }.bind(this)
            }
        }));

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_setters'),
            name : 'setter',
            length : 255,
            value : record.data.setter,
            store : pimcore.globalmanager.get('importdefinitions_setters'),
            valueField : 'setter',
            displayField : 'setter',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.getSetterPanel().removeAll();

                    this.getSetterPanelLayout(newValue);
                }.bind(this)
            }
        }));

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_getters'),
            name : 'getter',
            length : 255,
            value : record.data.getter,
            store : pimcore.globalmanager.get('importdefinitions_getters'),
            valueField : 'getter',
            displayField : 'getter',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.getGetterPanel().removeAll();

                    this.getGetterPanelLayout(newValue);
                }.bind(this)
            }
        }));

        this.configForm = new Ext.form.FormPanel({
            items : fieldSetItems,
            layout: 'form',
            defaults: { anchor: '100%' },
            title : t('settings')
        });

        this.configPanel = new Ext.panel.Panel({
            layout: 'form',
            scrollable : true,
            items:
                [
                    this.configForm,
                    this.getInterpreterPanel(),
                    this.getReverseInterpreterPanel(),
                    this.getSetterPanel(),
                    this.getGetterPanel()
                ],
            buttons: [{
                text: t('apply'),
                iconCls: 'pimcore_icon_apply',
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            renderTo: 'pimcore_body',
            width: 800,
            height: 600,
            resizeable : true,
            modal: false,
            title: t('importdefinitions_config') + ' ' + fromColumn.data.label + ' => ' + toColumn.data.label,
            layout: 'fit',
            items: [this.configPanel]
        });

        this.getInterpreterPanelLayout(record.data.interpreter);
        this.getReverseInterpreterPanelLayout(record.data.reverseInterpreter);
        this.getSetterPanelLayout(record.data.setter);
        this.getGetterPanelLayout(record.data.getter);

        this.window.show();
    },

    getInterpreterPanel : function () {
        if (!this.interpreterPanel) {
            this.interpreterPanel = new Ext.form.FormPanel({
                defaults: { anchor: '90%' },
                layout: 'form',
                title : t('importdefinitions_interpreter_settings')
            });
        }

        return this.interpreterPanel;
    },

    getInterpreterPanelLayout : function (type) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.importdefinitions.interpreters[type]) {
                this.interpreter = new pimcore.plugin.importdefinitions.interpreters[type];

                this.getInterpreterPanel().add(this.interpreter.getLayout(this.fromColumn, this.toColumn, this.record, Ext.isObject(this.record.data.interpreterConfig) ? this.record.data.interpreterConfig : {}, this.config));
                this.getInterpreterPanel().show();
            } else {
                this.getInterpreterPanel().hide();

                this.interpreter = null;
            }
        } else {
            this.getInterpreterPanel().hide();
        }
    },

    getReverseInterpreterPanel : function () {
        if (!this.reverseInterpreterPanel) {
            this.reverseInterpreterPanel = new Ext.form.FormPanel({
                defaults: { anchor: '90%' },
                layout: 'form',
                title : t('importdefinitions_reverse_interpreter_settings')
            });
        }

        return this.reverseInterpreterPanel;
    },

    getReverseInterpreterPanelLayout : function (type) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.importdefinitions.reverse_interpreters[type]) {
                this.reverseInterpreter = new pimcore.plugin.importdefinitions.reverse_interpreters[type];

                this.getReverseInterpreterPanel().add(this.reverseInterpreter.getLayout(this.fromColumn, this.toColumn, this.record, Ext.isObject(this.record.data.reverseInterpreterConfig) ? this.record.data.reverseInterpreterConfig : {}, this.config));
                this.getReverseInterpreterPanel().show();
            } else {
                this.getReverseInterpreterPanel().hide();

                this.reverseInterpreter = null;
            }
        } else {
            this.getReverseInterpreterPanel().hide();
        }
    },

    getSetterPanel : function () {
        if (!this.setterPanel) {
            this.setterPanel = new Ext.form.FormPanel({
                defaults: { anchor: '100%' },
                layout: 'form',
                title : t('importdefinition_setter_settings')
            });
        }

        return this.setterPanel;
    },

    getSetterPanelLayout : function (type) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.importdefinitions.setters[type]) {
                this.setter = new pimcore.plugin.importdefinitions.setters[type];

                this.getSetterPanel().add(this.setter.getLayout(this.fromColumn, this.toColumn, this.record, Ext.isObject(this.record.data.setterConfig) ? this.record.data.setterConfig : {}, this.config));
                this.getSetterPanel().show();
            } else {
                this.getSetterPanel().hide();

                this.setter = null;
            }
        } else {
            this.getSetterPanel().hide();
        }
    },

    getGetterPanel : function () {
        if (!this.getterPanel) {
            this.getterPanel = new Ext.form.FormPanel({
                defaults: { anchor: '100%' },
                layout: 'form',
                title : t('importdefinition_getter_settings')
            });
        }

        return this.getterPanel;
    },

    getGetterPanelLayout : function (type) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.importdefinitions.getters[type]) {
                this.getter = new pimcore.plugin.importdefinitions.getters[type];

                this.getGetterPanel().add(this.getter.getLayout(this.fromColumn, this.toColumn, this.record, Ext.isObject(this.record.data.getterConfig) ? this.record.data.getterConfig : {}, this.config));
                this.getGetterPanel().show();
            } else {
                this.getGetterPanel().hide();

                this.getter = null;
            }
        } else {
            this.getGetterPanel().hide();
        }
    },

    commitData: function () {
        var form = this.configForm.getForm();
        var interpreterForm = this.getInterpreterPanel().getForm();
        var reverseInterpreterForm = this.getReverseInterpreterPanel().getForm();
        var setterForm = this.getSetterPanel().getForm();
        var getterForm = this.getGetterPanel().getForm();

        if (form.isValid() && interpreterForm.isValid() && setterForm.isValid()) {
            Ext.Object.each(form.getFieldValues(), function (key, value) {
                this.record.data[key] = value;
            }.bind(this));

            this.record.data.interpreterConfig = {};
            this.record.data.reverseInterpreterConfig = {};
            this.record.data.setterConfig = {};
            this.record.data.getterConfig = {};

            if (this.getInterpreterPanel().isVisible()) {
                if (Ext.isFunction(this.interpreter.getInterpreterData)) {
                    this.record.data.interpreterConfig = this.interpreter.getInterpreterData();
                }
                else {
                    Ext.Object.each(interpreterForm.getFieldValues(), function (key, value) {
                        this.record.data.interpreterConfig[key] = value;
                    }.bind(this));
                }
            }

            if (this.getReverseInterpreterPanel().isVisible()) {
                if (Ext.isFunction(this.reverseInterpreter.getInterpreterData)) {
                    this.record.data.reverseInterpreterConfig = this.reverseInterpreter.getInterpreterData();
                }
                else {
                    Ext.Object.each(reverseInterpreterForm.getFieldValues(), function (key, value) {
                        this.record.data.reverseInterpreterConfig[key] = value;
                    }.bind(this));
                }
            }

            if (this.getSetterPanel().isVisible()) {
                if (Ext.isFunction(this.setter.getSetterData)) {
                    this.record.data.setterConfig = this.setter.getSetterData();
                }
                else {
                    Ext.Object.each(setterForm.getFieldValues(), function (key, value) {
                        this.record.data.setterConfig[key] = value;
                    }.bind(this));
                }

            }

            if (this.getGetterPanel().isVisible()) {
                if (Ext.isFunction(this.getter.getGetterData)) {
                    this.record.data.getterConfig = this.getter.getGetterData();
                }
                else {
                    Ext.Object.each(getterForm.getFieldValues(), function (key, value) {
                        this.record.data.getterConfig[key] = value;
                    }.bind(this));
                }

            }

            this.window.close();
        }
    }
});
