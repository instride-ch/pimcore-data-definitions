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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.asset_url');

pimcore.plugin.datadefinitions.interpreters.asset_url = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        var deduplicateByUrlEnabled = Ext.isDefined(config.deduplicate_by_url) ? config.deduplicate_by_url : false
        var deduplicateByHashEnabled = Ext.isDefined(config.deduplicate_by_hash) ? config.deduplicate_by_hash : false
        var relocateExistingCheckbox = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('data_definitions_relocate_existing_objects'),
            name: 'relocate_existing_objects',
            value: config.deduplicate_by_url && Ext.isDefined(config.relocate_existing_objects) ? config.relocate_existing_objects : false,
            disabled: deduplicateByUrlEnabled === false || deduplicateByHashEnabled === false
        });

        var renameExistingCheckbox = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('data_definitions_rename_existing_objects'),
            name: 'rename_existing_objects',
            value: config.deduplicate_by_url && Ext.isDefined(config.rename_existing_objects) ? config.rename_existing_objects : false,
            disabled: deduplicateByUrlEnabled === false || deduplicateByHashEnabled === false
        });

        return [{
            xtype: 'textfield',
            fieldLabel: t('data_definitions_asset_save_path'),
            name: 'path',
            width: 500,
            value: config.path || null,
            cls: 'input_drop_target',
            ddValidator: {
                elementType: 'asset'
            },
            canDrop: function (data) {
                if (!data.records[0] || !data.records[0].data) {
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
                xtype: 'checkbox',
                fieldLabel: t('data_definitions_interpreter_asset_url_deduplicate_by_url'),
                name: 'deduplicate_by_url',
                value: deduplicateByUrlEnabled,
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
            {
                xtype: 'checkbox',
                fieldLabel: t('data_definitions_interpreter_asset_url_deduplicate_by_hash'),
                name: 'deduplicate_by_hash',
                value: deduplicateByHashEnabled,
                listeners: {
                    change: function (el, enabled) {
                        var isDeduplicateByHashDisabled = (enabled === false);

                        relocateExistingCheckbox
                            .setValue(false)
                            .setDisabled(isDeduplicateByHashDisabled);

                        renameExistingCheckbox
                            .setValue(false)
                            .setDisabled(isDeduplicateByHashDisabled);
                    }
                }
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('data_definitions_interpreter_asset_url_use_content_disposition'),
                name: 'use_content_disposition',
                value: Ext.isDefined(config.use_content_disposition) ? config.use_content_disposition : false
            },
            relocateExistingCheckbox,
            renameExistingCheckbox
        ];
    }
});
