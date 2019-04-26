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

pimcore.registerNS('pimcore.plugin.importdefinitions.export.fields');

pimcore.plugin.importdefinitions.export.fields = Class.create({
    id: null,

    initialize: function (data) {
        this.id = data.id;
        this.data = data;
    },

    getLayout: function () {
        this.configPanel = new Ext.Panel({
            layout: 'border',
            items: []
        });

        Ext.Ajax.request({
            url: '/admin/import_definitions/export_definitions/get-columns',
            params: {
                id: this.id
            },
            success: function(response) {
                var mapping = Ext.decode(response.responseText);
                var mappingIdentifier = {};
                var tree = {};

                Ext.each(mapping, function(record) {
                    if (!tree.hasOwnProperty(record.group)) {
                        tree[record.group] = {
                            'childs': [],
                            'nodeLabel': record.group,
                            'nodeType': record.type
                        };
                    }

                    tree[record.group]['childs'].push(record);

                    mappingIdentifier[record.identifier] = record;
                });

                this.tree = tree;
                this.mapping = mapping;
                this.mappingIdentifier = mappingIdentifier;

                this.configPanel.add([this.getClassDefinitionTreePanel(), this.getSelectionPanel()]);
            }.bind(this)
        });


        return this.configPanel;
    },

    getData: function () {
        var columns = [];

        if (this.selectionPanel) {
            var allowedColumns = [
                'fromColumn', 'toColumn', 'getter', 'getterConfig', 'interpreter', 'interpreterConfig'
            ];

            this.selectionPanel.getRootNode().eachChild(function (child) {
                var obj = {
                    type: child.data.objectType,
                    objectKey: child.data.name
                };

                Ext.Object.each(Ext.Object.merge(child.data, {}), function (key, value) {

                    if (key === 'configuration') {
                        var configuration = {};

                        Ext.Object.each(value, function (ckey, cvalue) {
                            if (cvalue) {
                                configuration[ckey] = cvalue;
                            }
                        });

                        value = configuration;

                        if (Object.keys(configuration).length === 0) {
                            return;
                        }
                    }

                    if (value && allowedColumns.indexOf(key) >= 0) {
                        obj[key] = value;
                    }
                });

                if (!obj.hasOwnProperty('getter') && obj.hasOwnProperty('getterConfig')) {
                    delete obj['getterConfig'];
                }

                if (!obj.hasOwnProperty('interpreter') && obj.hasOwnProperty('interpreterConfig')) {
                    delete obj['interpreterConfig'];
                }

                columns.push(obj);
            }.bind(this));
        }

        return columns;
    },

    getSelectionPanel: function () {
        if (!this.selectionPanel) {

            var childs = [];

            if (this.data.mapping) {
                for (var i = 0; i < this.data.mapping.length; i++) {
                    var map = this.data.mapping[i];

                    if (!this.mappingIdentifier.hasOwnProperty(map.fromColumn)) {
                        continue;
                    }

                    var fromColumn = this.mappingIdentifier[map.fromColumn].fromColumn;

                    var child = Ext.Object.merge(map,
                        {
                            text: map.fromColumn + ' => ' + map.toColumn,
                            type: 'data',
                            leaf: true,
                            iconCls: 'pimcore_icon_' + fromColumn.fieldtype,
                            key: fromColumn.name,
                            _fromColumn: fromColumn
                        }
                    );

                    childs.push(child);
                }
            }

            this.selectionPanel = new Ext.tree.TreePanel({
                bufferedRenderer: false,
                root: {
                    id: '0',
                    root: true,
                    text: t('importdefinitions_mapping_settings'),
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: childs
                },

                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        ddGroup: 'columnconfigelement'
                    },
                    listeners: {
                        beforedrop: function (node, data, overModel, dropPosition, dropHandlers, eOpts) {
                            var target = overModel.getOwnerTree().getView();
                            var source = data.view;

                            if (target !== source) {
                                var record = data.records[0];
                                var copy = record.createNode(Ext.apply({}, {
                                    fromColumn: record.get('fromColumn').identifier,
                                    toColumn: record.get('fromColumn').identifier,
                                    leaf: true,
                                    iconCls: record.get('iconCls'),
                                    text: record.get('text'),
                                    getter: record.get('getter'),
                                    getterConfig: record.get('getterConfig'),
                                    interpreter: record.get('interpreter'),
                                    interpreterConfig: record.get('interpreterConfig'),
                                    _fromColumn: record.get('fromColumn')
                                }));

                                var dialog = new pimcore.plugin.importdefinitions.export.configDialog();
                                dialog.getConfigDialog(copy.get('_fromColumn'), copy, this.mapping);

                                data.records = [copy]; // assign the copy as the new dropNode
                            }
                        }.bind(this),
                        options: {
                            target: this.selectionPanel
                        }
                    }
                },
                region: 'east',
                title: t('importdefinitions_mapping_settings'),
                layout: 'fit',
                width: 428,
                split: true,
                autoScroll: true,
                listeners: {
                    itemcontextmenu: this.onTreeNodeContextmenu.bind(this)
                }
            });
            var store = this.selectionPanel.getStore();
            var model = store.getModel();
            model.setProxy({
                type: 'memory'
            });
        }

        return this.selectionPanel;
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();

        tree.select();

        var menu = new Ext.menu.Menu();

        if (this.id != 0) {
            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: 'pimcore_icon_delete',
                handler: function (node) {
                    this.selectionPanel.getRootNode().removeChild(record, true);
                }.bind(this, record)
            }));
            menu.add(new Ext.menu.Item({
                text: t('edit'),
                iconCls: 'pimcore_icon_edit',
                handler: function (node) {
                    var dialog = new pimcore.plugin.importdefinitions.export.configDialog();
                    dialog.getConfigDialog(node.get('_fromColumn'), node, this.data);
                }.bind(this, record)
            }));
        }

        menu.showAt(e.pageX, e.pageY);
    },

    /*
     *       FIELD-TREE
     *
     **/
    getClassDefinitionTreePanel: function () {
        var tree = new Ext.tree.TreePanel({
            title: t('class_definitions'),
            region: 'center',

            //ddGroup: "columnconfigelement",
            autoScroll: true,
            rootVisible: false,
            root: {
                id: '0',
                root: true,
                text: t('base'),
                allowDrag: false,
                leaf: true,
                isTarget: true
            },
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    enableDrag: true,
                    enableDrop: false,
                    ddGroup: 'columnconfigelement'
                }
            }
        });

        tree.addListener('itemdblclick', function (tree, record, item, index, e, eOpts) {
            if (!record.data.root && record.datatype !== 'layout'
                && record.data.dataType !== 'localizedfields') {
                var copy = record.createNode(Ext.apply({}, {
                    fromColumn: record.get('fromColumn').identifier,
                    toColumn: record.get('fromColumn').identifier,
                    leaf: true,
                    iconCls: record.get('iconCls'),
                    text: record.get('text'),
                    getter: record.get('getter'),
                    getterConfig: record.get('getterConfig'),
                    interpreter: record.get('interpreter'),
                    interpreterConfig: record.get('interpreterConfig'),
                    _fromColumn: record.get('fromColumn')
                }));

                this.selectionPanel.getRootNode().appendChild(copy);

                var dialog = new pimcore.plugin.importdefinitions.export.configDialog();
                dialog.getConfigDialog(copy.get('_fromColumn'), copy, this.mapping);
            }
        }.bind(this));

        var keys = Object.keys(this.tree);
        for (var i = 0; i < keys.length; i++) {
            if (this.tree[keys[i]]) {
                if (this.tree[keys[i]].childs) {
                    var text = t(this.tree[keys[i]].nodeLabel);

                    if (this.tree[keys[i]].nodeType === 'objectbricks') {
                        text = ts(this.tree[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    if (this.tree[keys[i]].nodeType === 'classificationstore') {
                        text = ts(this.tree[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    if (this.tree[keys[i]].nodeType === 'fieldcollections') {
                        text = ts(this.tree[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    var baseNode = {
                        type: 'layout',
                        allowDrag: false,
                        iconCls: 'pimcore_icon_' + this.tree[keys[i]].nodeType,
                        text: text
                    };

                    baseNode = tree.getRootNode().appendChild(baseNode);
                    for (var j = 0; j < this.tree[keys[i]].childs.length; j++) {
                        var node = this.addDataChild.call(baseNode, this.tree[keys[i]].childs[j].fieldtype, this.tree[keys[i]].childs[j], this.tree[keys[i]].nodeType);

                        baseNode.appendChild(node);
                    }

                    baseNode.collapse();
                }
            }
        }

        tree.getRootNode().expand();

        return tree;
    },

    addDataChild: function (type, initData, objectType) {

        if (type !== 'objectbricks' && !initData.invisible) {
            var isLeaf = true;
            var draggable = true;

            var key = initData.identifier + ' (' + initData.label + ')';

            var newNode = Ext.Object.merge(initData, {
                text: key,
                objectKey: initData.name,
                key: initData.name,
                type: 'data',
                fromColumn: initData,
                toColumn: {},
                leaf: isLeaf,
                allowDrag: draggable,
                dataType: type,
                iconCls: 'pimcore_icon_' + type,
                expanded: true,
                objectType: objectType
            });

            newNode = this.appendChild(newNode);

            if (this.rendered) {
                this.expand();
            }

            return newNode;
        } else {
            return null;
        }

    }
});
