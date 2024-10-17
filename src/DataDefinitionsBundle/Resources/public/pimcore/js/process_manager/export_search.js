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

pimcore.object.search = Class.create(pimcore.object.search, {
    createGrid: function ($super, fromConfig, response, settings, save) {
        if (!Ext.ClassManager.get('Executable')) {
            Ext.define('Executable', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'name', type: 'string'},
                ]
            });
        }

        this.exportFromHere = new Ext.SplitButton({
            text: t('data_definitions_processmanager_export_from_here'),
            iconCls: "pimcore_icon_object pimcore_icon_overlay_add",
            menu: []
        });

        var $this = this;
        Ext.create('Ext.data.Store', {
            model: 'Executable',
            proxy: {
                type: 'ajax',
                url: '/admin/process_manager/executables/list-by-type',
                extraParams: {
                    type: 'exportdefinition'
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            sorters: [{
                property: 'name',
                direction: 'ASC'
            }],
            sortRoot: 'data',
            autoLoad: true,
            listeners: {
                refresh: function (store) {
                    var exportMenu = [];
                    store.each(function (executable) {
                        exportMenu.push({
                            text: executable.get('name'),
                            iconCls: "pimcore_icon_object pimcore_icon_overlay_add",
                            handler: $this.exportObjects.bind($this, executable)
                        });
                    });

                    if (exportMenu) {
                        $this.exportFromHere.down("menu").add(exportMenu);
                    }
                }
            }
        });

        $super(fromConfig, response, settings, save);
        this.grid.down("toolbar").add([
            "-",
            this.exportFromHere,
            "-"
        ]);
    },

    exportObjects: function (executable, menuItem) {
        var selected = this.grid.getSelectionModel().getSelection(), ids = [];
        if (selected) {
            ids = selected.map(function (item) {
                return item.id;
            });
        }

        Ext.Ajax.request({
            url: '/admin/process_manager/executables/run',
            params: {
                id: executable.id,
                startupConfig: Ext.encode({
                    root: this.object.id,
                    query: this.searchField.getValue(),
                    only_direct_children: this.checkboxOnlyDirectChildren.getValue(),
                    ids: ids,
                }),
                csrfToken: pimcore.settings['csrfToken']
            },
            method: 'POST',
            success: function (result) {
                result = Ext.decode(result.responseText);

                if (result.success) {
                    Ext.Msg.alert(t('success'), t('processmanager_executable_started'));
                } else {
                    Ext.Msg.alert(t('error'), result.message);
                }
            }.bind(this)
        });
    }
});
