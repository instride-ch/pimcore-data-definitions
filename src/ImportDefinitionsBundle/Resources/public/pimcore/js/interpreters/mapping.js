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

pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.mapping');

pimcore.plugin.importdefinitions.interpreters.mapping = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
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
                header: t('importdefinitions_interpreter_mapping_from'),
                dataIndex: 'from',
                flex: 1,
                editor: {
                    // defaults to textfield if no xtype is supplied
                    allowBlank: false
                }
            }, {
                header: t('importdefinitions_interpreter_mapping_to'),
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
            xtype : 'checkbox',
            fieldLabel: t('importdefinitions_interpreter_mapping_null_when_not_found'),
            name: 'return_null_when_not_found',
            value : Ext.isDefined(config.return_null_when_not_found) ? config.return_null_when_not_found : true
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
            mapping: this.store.getRange().map(function(rec) {
                return {
                    from: rec.data.from,
                    to: rec.data.to
                };
            })
        };
    }
});
