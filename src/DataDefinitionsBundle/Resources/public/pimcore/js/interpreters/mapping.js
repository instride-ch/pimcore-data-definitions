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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.mapping');

pimcore.plugin.datadefinitions.interpreters.mapping = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        var me = this;

        me.store = Ext.create('Ext.data.Store', {
            autoDestroy: true,
            proxy: {
                type: 'memory'
            },
            fields: ['from', 'to'],
            data: config && config.mapping ? config.mapping : []
        });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false
        });

        var grid = Ext.create('Ext.grid.Panel', {
            store: me.store,
            columns: [{
                header: t('data_definitions_interpreter_mapping_from'),
                dataIndex: 'from',
                flex: 1,
                editor: {
                    // defaults to textfield if no xtype is supplied
                    allowBlank: false
                }
            }, {
                header: t('data_definitions_interpreter_mapping_to'),
                dataIndex: 'to',
                flex: 1,
                editor: {
                    allowBlank: false
                }
            }],
            tbar: [{
                text: t('add'),
                iconCls: 'pimcore_icon_add',
                handler: function () {
                    rowEditing.cancelEdit();

                    // Create a model instance
                    var r = {
                        from: '',
                        to: ''
                    };

                    me.store.insert(0, r);
                    rowEditing.startEdit(0, 0);
                }
            }, {
                itemId: 'removeMapping',
                text: t('remove'),
                iconCls: 'pimcore_icon_delete',
                handler: function () {
                    var sm = grid.getSelectionModel();
                    rowEditing.cancelEdit();
                    me.store.remove(sm.getSelection());
                    if (me.store.getCount() > 0) {
                        sm.select(0);
                    }
                },
                disabled: true
            }],
            plugins: [rowEditing],
            listeners: {
                'selectionchange': function (view, records) {
                    grid.down('#removeMapping').setDisabled(!records.length);
                }
            }
        });

        me.checkbox = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('data_definitions_interpreter_mapping_null_when_not_found'),
            name: 'return_null_when_not_found',
            value: Ext.isDefined(config.return_null_when_not_found) ? config.return_null_when_not_found : true
        });

        return new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            border: false,
            items: [
                me.checkbox,
                grid
            ]
        });
    },

    getInterpreterData: function () {
        return {
            mapping: this.store.getRange().map(function (rec) {
                return {
                    from: rec.data.from,
                    to: rec.data.to
                };
            }),
            return_null_when_not_found: this.checkbox.getSubmitValue()
        };
    }
});
