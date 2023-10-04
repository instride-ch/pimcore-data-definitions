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
