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

pimcore.registerNS('pimcore.plugin.importdefinitions.provider.excel');

pimcore.plugin.importdefinitions.provider.excel = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function () {
        return [{
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_excel_headers'),
            name : 'excelHeaders',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['excelHeaders'] ? this.data.excelHeaders : ''
        }, {
            fieldLabel: t('importdefinitions_excel_file'),
            name: 'exampleFile',
            cls: 'input_drop_target',
            value: this.data['exampleFile'] ? this.data.exampleFile : '',
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

                            if (data.elementType == 'asset') {
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            }

                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                        },

                        onNodeDrop: function (target, dd, e, data) {
                            data = data.records[0].data;

                            if (data.elementType == 'asset') {
                                this.setValue(data.id);
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
