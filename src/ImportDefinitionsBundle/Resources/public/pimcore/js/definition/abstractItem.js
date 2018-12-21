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

pimcore.registerNS('pimcore.plugin.importdefinitions.definition.abstractItem');

pimcore.plugin.importdefinitions.definition.abstractItem = Class.create(coreshop.resource.item, {
    getPanel: function () {
        var me = this,
            panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name + ' (' + this.data.id + ')',
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            buttons: [
                {
                    text: t('importdefinitions_import_definition'),
                    iconCls: 'pimcore_icon_import',
                    handler: this.upload.bind(this)
                },
                {
                    text: t('importdefinitions_export_definition'),
                    iconCls: 'pimcore_icon_export',
                    handler: function () {
                        var id = this.data.id;
                        pimcore.helpers.download(this.url.export + '?id=' + id);
                    }.bind(this)
                },
                {
                    text: t('importdefinitions_duplicate_definition'),
                    iconCls: 'pimcore_icon_copy',
                    handler: function () {
                        var id = me.data.id;

                        Ext.MessageBox.prompt(t('add'), t('coreshop_enter_the_name'), function(button, value) {
                            Ext.Ajax.request({
                                url: me.url.duplicate,
                                jsonData: {
                                    id: id,
                                    name: value
                                },
                                method: 'post',
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    me.parentPanel.grid.getStore().reload();
                                    me.parentPanel.refresh();

                                    if (!data || !data.success) {
                                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                                    } else {
                                        me.parentPanel.openItem(data.data);
                                    }
                                }.bind(this)
                            });
                        }, null, null, '');
                    }.bind(this)
                },
                {
                    text: t('save'),
                    iconCls: 'pimcore_icon_apply',
                    handler: this.save.bind(this)
                }],
            items: this.getItems()
        });

        return panel;
    },

    getItems: function () {
        return [
            this.getSettings(),
            this.getProviderSettings(),
            this.getMappingSettings()
        ];
    },

    getProviderSettings: function () {
        if (!this.providerSettings) {
            this.providerSettings = Ext.create({
                xtype: 'panel',
                layout: 'border',
                title: t('importdefinitions_provider_settings'),
                iconCls: 'importdefinitions_icon_provider',
                disabled: true
            });
        }

        if (this.data.provider) {
            this.reloadProviderSettings(this.data.provider);
        }

        return this.providerSettings;
    },

    upload: function (callback) {
        pimcore.helpers.uploadDialog(this.url.upload + '?id=' + this.data.id, 'Filedata', function () {
            this.panel.destroy();
            this.parentPanel.openItem(this.data);
        }.bind(this), function () {
            Ext.MessageBox.alert(t('error'), t('error'));
        });
    }
});
