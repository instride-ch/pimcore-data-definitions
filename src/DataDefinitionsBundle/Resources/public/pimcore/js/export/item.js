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

pimcore.registerNS('pimcore.plugin.datadefinitions.export.item');

pimcore.plugin.datadefinitions.export.item = Class.create(pimcore.plugin.datadefinitions.definition.abstractItem, {
    iconCls: 'data_definitions_icon_export_definition',
    url: {
        save: '/admin/data_definitions/export_definitions/save',
        upload: '/admin/data_definitions/export_definitions/import',
        export: '/admin/data_definitions/export_definitions/export',
        duplicate: '/admin/data_definitions/export_definitions/duplicate'
    },

    providers: [],

    saveDisabled: function () {
        return !this.data.isWriteable;
    },

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
            iconCls: 'data_definitions_icon_settings',
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
                    fieldLabel: t('data_definitions_provider'),
                    name: 'provider',
                    displayField: 'provider',
                    valueField: 'provider',
                    store: pimcore.globalmanager.get('data_definitions_export_providers'),
                    value: this.data.provider,
                    width: 500,
                    listeners: {
                        change: function (combo, value) {
                            this.reloadProviderSettings(value);
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('data_definitions_fetcher'),
                    name: 'fetcher',
                    displayField: 'fetcher',
                    valueField: 'fetcher',
                    store: pimcore.globalmanager.get('data_definitions_fetchers'),
                    value: this.data.fetcher,
                    width: 500,
                    listeners: {
                        change: function (combo, newValue) {
                            this.getFetcherPanel().removeAll();

                            this.getFetcherPanelLayout(newValue);
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('class'),
                    name: 'class',
                    displayField: 'text',
                    valueField: 'text',
                    store: classesStore,
                    width: 500,
                    value: this.data.class,
                    listeners: {
                        change: function (combo, value) {
                            this.reloadColumnMapping();
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('data_definitions_runner'),
                    name: 'runner',
                    displayField: 'runner',
                    valueField: 'runner',
                    store: pimcore.globalmanager.get('data_definitions_runners'),
                    value: this.data.runner,
                    width: 500,
                    listeners: {
                        change: function (combo, value) {
                            this.data.runner = value;
                        }.bind(this)
                    }
                },
                {
                    fieldLabel: t('data_definitions_stop_on_exception'),
                    xtype: 'checkbox',
                    name: 'stopOnException',
                    checked: this.data.stopOnException
                },
                {
                    fieldLabel: t('data_definitions_enable_inheritance'),
                    xtype: 'checkbox',
                    name: 'enableInheritance',
                    checked: this.data.enableInheritance
                },
                {
                    fieldLabel: t('data_definitions_fetcher_objects_unpublished'),
                    xtype: 'checkbox',
                    name: 'fetchUnpublished',
                    checked: this.data.fetchUnpublished
                },
                {
                    fieldLabel: t('data_definitions_failure_document'),
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
                    fieldLabel: t('data_definitions_success_document'),
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
                },
                this.getFetcherPanel(),
            ]
        });

        this.getFetcherPanelLayout(this.data.fetcher);

        return this.configForm;
    },

    reloadProviderSettings: function (provider) {
        if (this.providerSettings) {
            this.providerSettings.removeAll();

            if (pimcore.plugin.datadefinitions.export_provider[provider] !== undefined) {
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

    providerSettingsSuccess: function (providerPanel) {
        this.reloadColumnMapping();
    },

    updateProviderMapViews: function () {
        this.providerSettings.add(new pimcore.plugin.datadefinitions.export_provider[this.data.provider](this.data.configuration ? this.data.configuration : {}, this).getForm());
        this.providerSettings.enable();
    },

    getMappingSettings: function () {
        if (!this.mappingSettings) {
            this.mappingSettings = Ext.create({
                xtype: 'panel',
                title: t('data_definitions_mapping_settings'),
                iconCls: 'data_definitions_icon_mapping',
                disabled: true,
                border: false,
                layout: 'fit',
                autoScroll: true,
                forceLayout: true,
                defaults: {
                    forceLayout: true
                }
            });
        }

        if (this.data.class) {
            this.reloadColumnMapping();
        }

        return this.mappingSettings;
    },

    getFetcherPanel: function () {
        if (!this.fetcherPanel) {
            this.fetcherPanel = new Ext.form.FormPanel({
                defaults: {anchor: '100%'},
                layout: 'form',
                border: 1,
                padding: '0 0 10px 0',
                title: t('data_definitions_fetcher_settings')
            });
        }

        return this.fetcherPanel;
    },

    getFetcherPanelLayout: function (type) {
        if (type) {
            type = type.toLowerCase();

            if (pimcore.plugin.datadefinitions.fetchers[type]) {
                this.fetcher = new pimcore.plugin.datadefinitions.fetchers[type]();

                this.getFetcherPanel().add(this.fetcher.getLayout(Ext.isObject(this.data.fetcherConfig) ? this.data.fetcherConfig : {}, this.data));
                this.getFetcherPanel().show();
            } else {
                this.getFetcherPanel().hide();

                this.fetcher = null;
            }
        } else {
            this.getFetcherPanel().hide();
        }
    },

    reloadColumnMapping: function () {
        if (this.mappingSettings) {
            this.mappingSettings.removeAll();

            this.mappingSettingsFieldsPanel = new pimcore.plugin.datadefinitions.export.fields(this.data);
            this.mappingSettings.add(this.mappingSettingsFieldsPanel.getLayout());
            this.mappingSettings.enable();
        }
    },

    getSaveData: function () {
        var data = {
            configuration: {},
            fetcherConfig: {},
            mapping: this.mappingSettingsFieldsPanel.getData()
        };

        Ext.apply(data, this.configForm.getForm().getFieldValues());

        if (this.providerSettings.down('form')) {
            Ext.apply(data.configuration, this.providerSettings.down('form').getForm().getFieldValues());
        }

        if (this.getFetcherPanel().isVisible()) {
            if (Ext.isFunction(this.fetcher.getFetcherData)) {
                data.fetcherConfig = this.fetcher.getFetcherData();
            } else {
                Ext.Object.each(this.getFetcherPanel().getForm().getFieldValues(), function (key, value) {
                    data.fetcherConfig[key] = value;
                }.bind(this));
            }
        }

        return data;
    }
});
