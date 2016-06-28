pimcore.registerNS('pimcore.plugin.advancedimportexport.definition.configDialog');

pimcore.plugin.advancedimportexport.definition.configDialog = Class.create({

    getConfigDialog : function(fromColumn, toColumn, record) {
        var fieldSetItems = [];

        this.fromColumn = fromColumn;
        this.toColumn = toColumn;
        this.record = record;

        fieldSetItems.push(new Ext.form.TextField({
            fieldLabel : t('advancedimportexport_fromColumn'),
            name : 'fromColumn',
            length : 255,
            value : fromColumn.data.label,
            disabled : true
        }));

        fieldSetItems.push(new Ext.form.TextField({
            fieldLabel : t('advancedimportexport_toColumn'),
            name : 'fromColumn',
            length : 255,
            value : toColumn.data.label,
            disabled : true
        }));

        if(!Ext.isObject(record.data.config)) {
            record.data.config = {};
        }

        if(!record.data.config.interpreter) {
            if(toColumn.data.type === "objectbrick") {
                record.data.config.interpreter = "objectbrick";
            }
            else if(toColumn.data.type === "classificationstore") {
                record.data.config.interpreter = "classificationstore";
            }
        }

        fieldSetItems.push(new Ext.form.ComboBox({
            fieldLabel : t('advancedimportexport_interpreters'),
            name : 'interpreter',
            length : 255,
            value : record.data.config.interpreter,
            store : pimcore.globalmanager.get('advancedimportexport_interpreters'),
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
                    this.getInterpreterPanel()
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
            width: 400,
            height: 400,
            resizeable : true,
            modal: true,
            title: t('advancedimportexport_config') + ' ' + fromColumn.data.label + ' => ' + toColumn.data.label,
            layout: 'fit',
            items: [this.configPanel]
        });

        this.getInterpreterPanelLayout(record.data.config.interpreter);

        this.window.show();
    },

    getInterpreterPanel : function () {
        if (!this.interpreterPanel) {
            this.interpreterPanel = new Ext.form.FormPanel({
                defaults: { anchor: '90%' },
                layout: 'form',
                title : t('coreshop_index_interpreter_settings')
            });
        }

        return this.interpreterPanel;
    },

    getInterpreterPanelLayout : function (type) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.advancedimportexport.interpreters[type]) {
                var interpreter = new pimcore.plugin.advancedimportexport.interpreters[type];

                this.getInterpreterPanel().add(interpreter.getLayout(this.fromColumn, this.toColumn, this.record));
                this.getInterpreterPanel().show();
            } else {
                this.getInterpreterPanel().hide();
            }
        } else {
            this.getInterpreterPanel().hide();
        }
    },

    commitData: function () {
        var form = this.configForm.getForm();
        var interpreterForm = this.getInterpreterPanel().getForm();

        if(form.isValid() && interpreterForm.isValid()) {
            Ext.Object.each(form.getFieldValues(), function (key, value) {
                this.record.data.config[key] = value;
            }.bind(this));

            if (this.getInterpreterPanel().isVisible()) {
                Ext.Object.each(interpreterForm.getFieldValues(), function (key, value) {
                    this.record.data.config[key] = value;
                }.bind(this));
            }

            this.window.close();
        }
    }
});