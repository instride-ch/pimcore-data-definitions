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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.asset_url');
pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.asset_url');

pimcore.plugin.datadefinitions.interpreters.asset_url = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        var deduplicateByUrlEnabled = Ext.isDefined(config.deduplicate_by_url) ? config.deduplicate_by_url : false
        var relocateExistingCheckbox = Ext.create({
            xtype : 'checkbox',
            fieldLabel: t('data_definitions_relocate_existing_objects'),
            name: 'relocate_existing_objects',
            value : config.deduplicate_by_url && Ext.isDefined(config.relocate_existing_objects) ? config.relocate_existing_objects : false,
            disabled: deduplicateByUrlEnabled === false
        });

        var renameExistingCheckbox = Ext.create({
            xtype : 'checkbox',
            fieldLabel: t('data_definitions_rename_existing_objects'),
            name: 'rename_existing_objects',
            value : config.deduplicate_by_url && Ext.isDefined(config.rename_existing_objects) ? config.rename_existing_objects : false,
            disabled: deduplicateByUrlEnabled === false
        });

        return [{
            xtype : 'textfield',
            fieldLabel: t('data_definitions_asset_save_path'),
            name: 'path',
            width: 500,
            value : config.path || null,
            cls: 'input_drop_target',
            ddValidator: {
                elementType: 'asset'
            },
            canDrop: function(data)
            {
                if(!data.records[0] || !data.records[0].data) {
                    return false;
                }
                var recordData = data.records[0].data;
                return recordData.type === 'folder' && recordData.elementType === 'asset';
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
        },
        {
            xtype : 'checkbox',
            fieldLabel: t('data_definitions_interpreter_asset_url_deduplicate_by_url'),
            name: 'deduplicate_by_url',
            value : deduplicateByUrlEnabled,
            listeners: {
                change: function (el, enabled) {
                    var isDeduplicateByUrlDisabled = (enabled === false);

                    relocateExistingCheckbox
                        .setValue(false)
                        .setDisabled(isDeduplicateByUrlDisabled);

                    renameExistingCheckbox
                        .setValue(false)
                        .setDisabled(isDeduplicateByUrlDisabled);
                }
            }
        },
        relocateExistingCheckbox,
        renameExistingCheckbox
        ];
    }
});

pimcore.plugin.importdefinitions.interpreters.asset_url = Class.create(pimcore.plugin.datadefinitions.interpreters.asset_url, {});
