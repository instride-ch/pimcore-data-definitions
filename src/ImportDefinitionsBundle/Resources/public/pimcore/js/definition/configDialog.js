/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions.definition.configDialog');

pimcore.plugin.importdefinitions.definition.configDialog = Class.create({

    getConfigDialog : function (fromColumn, toColumn, record) {
        var fieldSetItems = [];

        this.fromColumn = fromColumn;
        this.toColumn = toColumn;
        this.record = record;

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

        if (!record.data.config.interpreter) {
            if (toColumn.data.fieldtype === 'quantityValue') {
                record.data.config.interpreter = 'quantityvalue';
            }
        }

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('importdefinitions_interpreters'),
            name : 'interpreter',
            length : 255,
            value : record.data.config.interpreter,
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
                    this.getSetterPanel()
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
            width: 800,
            height: 600,
            resizeable : true,
            modal: true,
            title: t('importdefinitions_config') + ' ' + fromColumn.data.label + ' => ' + toColumn.data.label,
            layout: 'fit',
            items: [this.configPanel]
        });

        this.getInterpreterPanelLayout(record.data.interpreter);
        this.getSetterPanelLayout(record.data.setter);

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
                var interpreter = new pimcore.plugin.importdefinitions.interpreters[type];

                this.getInterpreterPanel().add(interpreter.getLayout(this.fromColumn, this.toColumn, this.record, Ext.isObject(this.record.data.interpreterConfig) ? this.record.data.interpreterConfig : {}));
                this.getInterpreterPanel().show();
            } else {
                this.getInterpreterPanel().hide();
            }
        } else {
            this.getInterpreterPanel().hide();
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
                var setter = new pimcore.plugin.importdefinitions.setters[type];

                this.getSetterPanel().add(setter.getLayout(this.fromColumn, this.toColumn, this.record, Ext.isObject(this.record.data.setterConfig) ? this.record.data.setterConfig : {}));
                this.getSetterPanel().show();
            } else {
                this.getSetterPanel().hide();
            }
        } else {
            this.getSetterPanel().hide();
        }
    },

    commitData: function () {
        var form = this.configForm.getForm();
        var interpreterForm = this.getInterpreterPanel().getForm();
        var setterForm = this.getSetterPanel().getForm();

        if (form.isValid() && interpreterForm.isValid() && setterForm.isValid()) {
            Ext.Object.each(form.getFieldValues(), function (key, value) {
                this.record.data[key] = value;
            }.bind(this));

            if (this.getInterpreterPanel().isVisible()) {
                if (!Ext.isObject(this.record.data.interpreterConfig)) {
                    this.record.data.interpreterConfig = {};
                }

                if (Ext.isFunction(this.getInterpreterPanel().items.getAt(0).getInterpreterData)) {
                    this.record.data.interpreterConfig = this.getInterpreterPanel().items.getAt(0).getInterpreterData();
                }
                else {
                    Ext.Object.each(interpreterForm.getFieldValues(), function (key, value) {
                        this.record.data.interpreterConfig[key] = value;
                    }.bind(this));
                }
            }

            if (this.getSetterPanel().isVisible()) {
                if (!Ext.isObject(this.record.data.setterConfig)) {
                    this.record.data.setterConfig = {};
                }

                Ext.Object.each(setterForm.getFieldValues(), function (key, value) {
                    this.record.data.setterConfig[key] = value;
                }.bind(this));
            }

            this.window.close();
        }
    }
});
