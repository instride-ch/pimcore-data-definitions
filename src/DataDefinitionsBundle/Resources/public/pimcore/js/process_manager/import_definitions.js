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

document.addEventListener('processmanager.ready', function () {
    processmanager.executable.types.importdefinition = Class.create(pimcore.plugin.processmanager.executable.abstractType, {
        getItems: function () {
            pimcore.globalmanager.get('data_definitions_definitions').load();

            return [{
                xtype: 'combo',
                fieldLabel: t('data_definitions_import_definitions'),
                name: 'definition',
                displayField: 'name',
                valueField: 'id',
                store: pimcore.globalmanager.get('data_definitions_definitions'),
                value: this.data.settings.definition,
                allowBlank: false
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: t('data_definitions_processmanager_params'),
                layout: 'hbox',
                width: 500,
                value: this.data.settings.filePath,
                items: [{
                    xtype: "textfield",
                    name: 'params',
                    id: 'data_definitions_processmanager_params',
                    width: 450,
                    value: this.data.settings.params,
                    allowBlank: true
                }, {
                    xtype: "button",
                    text: t('find'),
                    iconCls: "pimcore_icon_search",
                    style: "margin-left: 5px",
                    handler: this.openSearchEditor.bind(this)
                },
                    {
                        xtype: "button",
                        text: t('upload'),
                        cls: "pimcore_inline_upload",
                        iconCls: "pimcore_icon_upload",
                        style: "margin-left: 5px",
                        handler: function (item) {
                            this.uploadDialog();
                        }.bind(this)
                    }]
            }];
        },

        uploadDialog: function () {
            pimcore.helpers.assetSingleUploadDialog("", "path", function (res) {
                try {
                    var data = Ext.decode(res.response.responseText);
                    if (data["id"]) {
                        this.setValue(data["fullpath"]);
                    }
                } catch (e) {
                    console.log(e);
                }
            }.bind(this));
        },

        openSearchEditor: function () {
            pimcore.helpers.itemselector(
                false,
                this.addDataFromSelector.bind(this),
                {
                    type: ["asset"],
                    subtype: {},
                    specific: {}
                }
            );
        },

        addDataFromSelector: function (data) {
            this.setValue(data.fullpath);
        },

        setValue: function (value) {
            var params = '{"file":"var/assets' + value + '"}';
            Ext.getCmp('data_definitions_processmanager_params').setValue(params);
        }
    });
});
