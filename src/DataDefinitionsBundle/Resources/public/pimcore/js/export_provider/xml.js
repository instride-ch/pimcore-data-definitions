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

pimcore.registerNS('pimcore.plugin.datadefinitions.export_provider.xml');

pimcore.plugin.datadefinitions.export_provider.xml = Class.create(pimcore.plugin.datadefinitions.export_provider.abstractprovider, {
    getItems: function () {
        return [{
            xtype: 'textfield',
            name: 'xsltPath',
            fieldLabel: t('data_definitions_xml_xslt_path'),
            value: this.data['xsltPath'] ? this.data.xsltPath : null,
            width: 500,
            cls: 'input_drop_target',
            ddValidator: {
                elementType: 'asset'
            },
            canDrop: function (data) {
                if (!data.records[0] || !data.records[0].data) {
                    return false;
                }
                var recordData = data.records[0].data;
                return recordData.elementType === 'asset' && recordData.type !== 'folder';
            },
            listeners: {
                'render': function (el) {
                    new Ext.dd.DropZone(el.getEl(), {
                        reference: this,
                        ddGroup: 'element',
                        getTargetFromEvent: function (e) {
                            return this.getEl();
                        }.bind(el),

                        onNodeOver: function (target, dd, e, data) {
                            if (this.canDrop(data)) {
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            } else {
                                return Ext.dd.DropZone.prototype.dropNotAllowed;
                            }
                        }.bind(el),

                        onNodeDrop: function (target, dd, e, data) {
                            if (this.canDrop(data)) {
                                this.setValue(data.records[0].data.path);

                                return true;
                            }
                            return false;
                        }.bind(el)
                    });
                }
            }
        }];
    }
});
