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

pimcore.registerNS('pimcore.plugin.importdefinitions.import.item');

pimcore.plugin.importdefinitions.import.item = Class.create(pimcore.plugin.importdefinitions.definition.abstractItem, {
    iconCls: 'importdefinitions_icon_import_definition',
    url: {
        save: '/admin/import_definitions/definitions/save',
        upload : '/admin/import_definitions/definitions/import',
        export : '/admin/import_definitions/definitions/export',
        duplicate : '/admin/import_definitions/definitions/duplicate',
        test: '/admin/import_definitions/definitions/test-data'
    },

    providers: [],

    getSettings: function () {
        var classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/class/get-tree'
            },
            fields: ['text']
        });
        classesStore.load();

        this.configForm = new Ext.form.Panel({
            bodyStyle: 'padding:10px;',
            title: t('settings'),
            iconCls: 'importdefinitions_icon_settings',
            autoScroll: true,
            defaults: {
                labelWidth: 200
            },
            border: false,
            items: [
                {
                    xtype: 'textfield',
                    fieldLabel: t('name'),
                    name: 'name',
                    width: 500,
                    value: this.data.name
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('importdefinitions_provider'),
                    name: 'provider',
                    displayField: 'provider',
                    valueField: 'provider',
                    store: pimcore.globalmanager.get('importdefinitions_providers'),
                    value: this.data.provider,
                    width: 500,
                    listeners: {
                        change: function (combo, value) {
                            this.reloadProviderSettings(value);
                            this.reloadColumnMapping();
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('importdefinitions_loader'),
                    name: 'loader',
                    displayField: 'loader',
                    valueField: 'loader',
                    store: pimcore.globalmanager.get('importdefinitions_loaders'),
                    value: this.data.loader,
                    width: 500,
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('class'),
                    name: 'class',
                    displayField: 'text',
                    valueField: 'text',
                    store: classesStore,
                    width: 500,
                    value: this.data.class
                },
                {
                    xtype: 'textfield',
                    fieldLabel: t('path'),
                    name: 'objectPath',
                    width: 500,
                    cls: 'input_drop_target',
                    value: this.data.objectPath,
                    listeners: {
                        'render': function (el) {
                            new Ext.dd.DropZone(el.getEl(), {
                                reference: this,
                                ddGroup: 'element',
                                getTargetFromEvent: function (e) {
                                    return this.getEl();
                                }.bind(el),

                                onNodeOver: function (target, dd, e, data) {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                },

                                onNodeDrop: function (target, dd, e, data) {
                                    var record = data.records[0];
                                    var data = record.data;

                                    if (data.elementType == 'object') {
                                        this.setValue(data.path);
                                        return true;
                                    }
                                    return false;
                                }.bind(el)
                            });
                        }
                    }
                },
                {
                    xtype: 'textfield',
                    fieldLabel: t('key'),
                    name: 'key',
                    width: 500,
                    value: this.data.key
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('importdefinitions_cleaner'),
                    name: 'cleaner',
                    displayField: 'cleaner',
                    valueField: 'cleaner',
                    store: pimcore.globalmanager.get('importdefinitions_cleaners'),
                    value: this.data.cleaner,
                    width: 500,
                    listeners: {
                        change: function (combo, value) {
                            this.data.cleaner = value;
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('importdefinitions_filter'),
                    name: 'filter',
                    displayField: 'filter',
                    valueField: 'filter',
                    store: pimcore.globalmanager.get('importdefinitions_filters'),
                    value: this.data.filter,
                    width: 500,
                    listeners: {
                        change: function (combo, value) {
                            this.data.filter = value;
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('importdefinitions_runner'),
                    name: 'runner',
                    displayField: 'runner',
                    valueField: 'runner',
                    store: pimcore.globalmanager.get('importdefinitions_runners'),
                    value: this.data.runner,
                    width: 500,
                    listeners: {
                        change: function (combo, value) {
                            this.data.runner = value;
                        }.bind(this)
                    }
                },
                {
                    fieldLabel: t('importdefinitions_relocate_existing_objects'),
                    xtype: 'checkbox',
                    name: 'relocateExistingObjects',
                    checked: this.data.relocateExistingObjects
                },
                {
                    fieldLabel: t('importdefinitions_rename_existing_objects'),
                    xtype: 'checkbox',
                    name: 'renameExistingObjects',
                    checked: this.data.renameExistingObjects
                },
                {
                    fieldLabel: t('importdefinitions_skip_existing_objects'),
                    xtype: 'checkbox',
                    name: 'skipExistingObjects',
                    checked: this.data.skipExistingObjects
                },
                {
                    fieldLabel: t('importdefinitions_skip_new_objects'),
                    xtype: 'checkbox',
                    name: 'skipNewObjects',
                    checked: this.data.skipNewObjects
                },
                {
                    fieldLabel: t('importdefinitions_create_version'),
                    xtype: 'checkbox',
                    name: 'createVersion',
                    checked: this.data.createVersion
                },
                {
                    fieldLabel: t('importdefinitions_stop_on_exception'),
                    xtype: 'checkbox',
                    name: 'stopOnException',
                    checked: this.data.stopOnException
                },
                {
                    fieldLabel: t('importdefinitions_omit_mandatory_check'),
                    xtype: 'checkbox',
                    name: 'omitMandatoryCheck',
                    checked: this.data.omitMandatoryCheck
                },
                {
                    fieldLabel: t('importdefinitions_force_load_object'),
                    xtype: 'checkbox',
                    name: 'forceLoadObject',
                    checked: this.data.forceLoadObject
                },
                {
                    fieldLabel: t('importdefinitions_failure_document'),
                    labelWidth: 350,
                    name: 'failureNotificationDocument',
                    fieldCls: 'pimcore_droptarget_input',
                    value: this.data.failureNotificationDocument,
                    xtype: 'textfield',
                    listeners: {
                        render: function (el) {
                            new Ext.dd.DropZone(el.getEl(), {
                                reference: this,
                                ddGroup: 'element',
                                getTargetFromEvent: function (e) {
                                    return this.getEl();
                                }.bind(el),

                                onNodeOver: function (target, dd, e, data) {
                                    data = data.records[0].data;

                                    if (data.elementType == 'document') {
                                        return Ext.dd.DropZone.prototype.dropAllowed;
                                    }

                                    return Ext.dd.DropZone.prototype.dropNotAllowed;
                                },

                                onNodeDrop: function (target, dd, e, data) {
                                    data = data.records[0].data;

                                    if (data.elementType == 'document') {
                                        this.setValue(data.id);
                                        return true;
                                    }

                                    return false;
                                }.bind(el)
                            });
                        }
                    }
                },
                {
                    fieldLabel: t('importdefinitions_success_document'),
                    labelWidth: 350,
                    name: 'successNotificationDocument',
                    fieldCls: 'pimcore_droptarget_input',
                    value: this.data.successNotificationDocument,
                    xtype: 'textfield',
                    listeners: {
                        render: function (el) {
                            new Ext.dd.DropZone(el.getEl(), {
                                reference: this,
                                ddGroup: 'element',
                                getTargetFromEvent: function (e) {
                                    return this.getEl();
                                }.bind(el),

                                onNodeOver: function (target, dd, e, data) {
                                    data = data.records[0].data;

                                    if (data.elementType == 'document') {
                                        return Ext.dd.DropZone.prototype.dropAllowed;
                                    }

                                    return Ext.dd.DropZone.prototype.dropNotAllowed;
                                },

                                onNodeDrop: function (target, dd, e, data) {
                                    data = data.records[0].data;

                                    if (data.elementType == 'document') {
                                        this.setValue(data.id);
                                        return true;
                                    }

                                    return false;
                                }.bind(el)
                            });
                        }
                    }
                }
            ]
        });

        return this.configForm;
    },

    reloadProviderSettings: function (provider) {
        if (this.providerSettings) {
            this.providerSettings.removeAll();

            if (pimcore.plugin.importdefinitions.provider[provider] !== undefined) {
                if (this.data.provider === null) {
                    this.data.provider = provider;
                    this.save(function () {
                        this.updateProviderMapViews();
                    }.bind(this));
                } else {
                    this.data.provider = provider;
                    this.updateProviderMapViews();
                }
            }
        }
    },

    postSave: function (res) {
        if (res.success) {
            this.undirtyMappingRecords();
            this.reloadColumnMapping();
        }
    },

    undirtyMappingRecords: function () {
        if (this.mappingSettings && this.mappingSettings.down('grid')) {
            var store = this.mappingSettings.down('grid').getStore();

            store.getRange().forEach(function (record) {
                record.commit();
            });
        }
    },

    providerSettingsSuccess: function (providerPanel) {
        this.reloadColumnMapping();
    },

    updateProviderMapViews: function () {
        this.providerSettings.add(new pimcore.plugin.importdefinitions.provider[this.data.provider](this.data.configuration ? this.data.configuration : {}, this).getForm());
        this.providerSettings.enable();
    },

    getMappingSettings: function () {
        if (!this.mappingSettings) {
            this.mappingSettings = Ext.create({
                xtype: 'panel',
                layout: 'border',
                title: t('importdefinitions_mapping_settings'),
                iconCls: 'importdefinitions_icon_mapping',
                disabled: true
            });
        }

        if (this.data.provider) {
            this.reloadColumnMapping();
        }

        return this.mappingSettings;
    },

    reloadColumnMapping: function () {
        if (this.mappingSettings) {
            this.mappingSettings.removeAll();

            if (this.data.provider) {
                this.mappingSettings.enable();

                Ext.Ajax.request({
                    url: '/admin/import_definitions/definitions/get-columns',
                    params: {
                        id: this.data.id
                    },
                    method: 'GET',
                    success: function (result) {
                        var config = Ext.decode(result.responseText);
                        var gridStoreData = [];

                        var fromColumnStore = new Ext.data.Store({
                            fields: [
                                'identifier',
                                'label'
                            ],
                            data: config.fromColumns
                        });

                        if (typeof config.toColumns == 'undefined') {
                            config.toColumns = [];
                        }
                        var toColumnStore = new Ext.data.Store({
                            data: config.toColumns
                        });

                        var gridStore = new Ext.data.Store({
                            grouper: {

                                groupFn: function (item) {
                                    var rec = toColumnStore.findRecord('identifier', item.data.toColumn);

                                    if (rec) {
                                        return rec.data.group;
                                    }
                                }
                            },
                            fields: [
                                'fromColumn',
                                'toColumn',
                                'primaryIdentifier'
                            ]
                        });

                        config.toColumns.forEach(function (col) {
                            gridStoreData.push({
                                toColumn: col.id
                            });
                        });

                        gridStore.loadRawData(config.mapping);

                        var cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing');

                        var grid = Ext.create({
                            xtype: 'grid',
                            region: 'center',
                            store: gridStore,
                            plugins: [cellEditingPlugin],
                            features: [{
                                ftype: 'grouping',

                                groupHeaderTpl: '{name}'
                            }],
                            columns: {
                                defaults: {},
                                items: [
                                    {
                                        text: t('importdefinitions_toColumn'),
                                        dataIndex: 'toColumn',
                                        flex: 1,
                                        renderer: function (val, metadata) {
                                            var rec = toColumnStore.findRecord('identifier', val, 0, false, false, true);

                                            if (rec) {
                                                metadata.tdCls = 'pimcore_icon_' + rec.data.fieldtype + ' td-icon';

                                                return rec.data.label;
                                            }

                                            return val;
                                        }
                                    },
                                    {
                                        text: t('importdefinitions_fromColumn'),
                                        dataIndex: 'fromColumn',
                                        flex: 1,
                                        renderer: function (val) {
                                            if (val) {
                                                var rec = fromColumnStore.findRecord('identifier', val, 0, false, false, true);

                                                if (rec)
                                                    return rec.get('label');
                                                else
                                                    return '<b style="color: red;">' + t('importdefinitions_invalid_mapping') + '</b>';
                                            }

                                            return null;
                                        },

                                        editor: {
                                            xtype: 'combo',
                                            store: fromColumnStore,
                                            mode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            editable: false,
                                            listeners: {
                                                change: function (combo, newValue, oldValue, eOpts) {
                                                    if (newValue === '') {
                                                        return;
                                                    }

                                                    var gridRecord = combo.up('grid').getSelectionModel().getSelection();

                                                    if (gridRecord.length > 0) {
                                                        gridRecord = gridRecord[0];

                                                        var fromColumn = fromColumnStore.findRecord('identifier', newValue, 0, false, false, true);
                                                        var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);

                                                        if (fromColumn && toColumn) {
                                                            var dialog = new pimcore.plugin.importdefinitions.import.configDialog();
                                                            dialog.getConfigDialog(fromColumn, toColumn, gridRecord, config);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'checkcolumn',
                                        text: t('importdefinitions_primaryIdentifier'),
                                        dataIndex: 'primaryIdentifier',
                                        editor: {
                                            xtype: 'checkbox'
                                        }
                                    },
                                    {
                                        xtype: 'gridcolumn',
                                        dataIndex: 'fromColumn',
                                        width: 50,
                                        align: 'right',
                                        renderer: function (value, metadata, record) {
                                            var fromColumnRecordNum = fromColumnStore.findExact('identifier', record.get('fromColumn'));
                                            var fromColumn = fromColumnStore.getAt(fromColumnRecordNum);

                                            var toColumnRecordNum = toColumnStore.findExact('identifier', record.get('toColumn'));
                                            var toColumn = toColumnStore.getAt(toColumnRecordNum);

                                            if (fromColumn && toColumn) {
                                                var id = Ext.id();

                                                Ext.defer(function () {
                                                    if (Ext.get(id)) {
                                                        new Ext.button.Button({
                                                            renderTo: id,
                                                            iconCls: 'pimcore_icon_edit',
                                                            flex: 1,
                                                            cls: 'importdefinitions-edit-button',
                                                            handler: function () {
                                                                var dialog = new pimcore.plugin.importdefinitions.import.configDialog();
                                                                dialog.getConfigDialog(fromColumn, toColumn, record, config);
                                                            }
                                                        });
                                                    }
                                                }, 200);

                                                return Ext.String.format('<div id="{0}"></div>', id);
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'gridcolumn',
                                        dataIndex: 'fromColumn',
                                        width: 50,
                                        align: 'right',
                                        renderer: function (value, metadata, record) {
                                            var fromColumn = fromColumnStore.findRecord('identifier', record.get('fromColumn'));
                                            var toColumn = toColumnStore.findRecord('identifier', record.get('toColumn'));

                                            if (value) {
                                                var id = Ext.id();

                                                Ext.defer(function () {
                                                    if (Ext.get(id)) {
                                                        new Ext.button.Button({
                                                            renderTo: id,
                                                            iconCls: 'pimcore_icon_delete',
                                                            flex: 1,
                                                            cls: 'importdefinitions-edit-button',
                                                            handler: function () {
                                                                record.set('fromColumn', null);
                                                            }
                                                        });
                                                    }
                                                }, 200);

                                                return Ext.String.format('<div id="{0}"></div>', id);
                                            }
                                        }
                                    }
                                ]
                            }

                        });

                        this.mappingSettings.add(grid);
                    }.bind(this)
                });
            }
        }
    },

    getSaveData: function () {
        var data = {
            configuration: {},
            mapping: []
        };

        if (this.mappingSettings.down('grid')) {
            var mapping = this.mappingSettings.down('grid').getStore().getRange();
            var mappingResult = [];

            mapping.forEach(function (map) {
                if (map.data.fromColumn) {
                    delete map.data.id;
                    mappingResult.push(map.data);
                }
            });

            Ext.apply(data.mapping, mappingResult);
        }

        Ext.apply(data, this.configForm.getForm().getFieldValues());

        if (this.providerSettings.down('form')) {
            Ext.apply(data.configuration, this.providerSettings.down('form').getForm().getFieldValues());
        }

        return data;
    }
});
