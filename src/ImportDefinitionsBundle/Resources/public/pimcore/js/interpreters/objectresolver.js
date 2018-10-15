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

pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.object_resolver');

pimcore.plugin.importdefinitions.interpreters.object_resolver = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        var classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/class/get-tree'
            },
            fields: ['text']
        });
        classesStore.load();

        var createMissingEnabled = Ext.isDefined(config.create_missing) ? config.create_missing : false;
        var matchUnpublishedEnabled = Ext.isDefined(config.match_unpublished) ? config.match_unpublished : false;
        
        var missingObjectPathTextfield = Ext.create({
            xtype: 'textfield',
            fieldLabel: t('importdefinitions_interpreter_object_resolver_object_path'),
            name: 'object_path',
            width: 500,
            value: config.object_path || null,
            cls: 'input_drop_target',
            disabled: createMissingEnabled === false,
            ddValidator: {
                elementType: 'object'
            },
            canDrop: function (data) {
                if (!data.records[0] || !data.records[0].data) {
                    return false;
                }
                var recordData = data.records[0].data;
                return recordData.type === 'folder' && recordData.elementType === 'object';
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
        });
        var createPublishedCheckbox = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('importdefinitions_interpreter_object_resolver_create_published'),
            name: 'create_published',
            value: Ext.isDefined(config.create_published) ? config.create_published : (Ext.isDefined(config.match_unpublished) ? config.match_unpublished : true),
            disabled: matchUnpublishedEnabled === false
        });
        
        return [{
                xtype : 'combo',
                fieldLabel: t('class'),
                name: 'class',
                displayField: 'text',
                valueField: 'text',
                store: classesStore,
                width: 500,
                value : config.class ? config.class : null
            },
            {
                xtype : 'textfield',
                fieldLabel: t('importdefinitions_interpreter_object_resolver_field'),
                name: 'field',
                width: 500,
                value : config.field ? config.field : null
            },
            {
                xtype : 'checkbox',
                fieldLabel: t('importdefinitions_interpreter_object_resolver_match_unpublished'),
                name: 'match_unpublished',
                value : Ext.isDefined(config.match_unpublished) ? config.match_unpublished : true,
                listeners: {
                    change: function (el, enabled) {
                        var matchUnpublishedDisabled = (enabled === false);
                        if (matchUnpublishedDisabled) {
                            createPublishedCheckbox.setValue(true);
                        }
                        createPublishedCheckbox.setDisabled(matchUnpublishedDisabled);
                    }
                }
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('importdefinitions_interpreter_object_resolver_create_missing'),
                name: 'create_missing',
                value: Ext.isDefined(config.create_missing) ? config.create_missing : false,
                listeners: {
                    change: function (el, enabled) {
                        var createMissingDisabled = (enabled === false);
                        missingObjectPathTextfield.setDisabled(createMissingDisabled);
                        createPublishedCheckbox.setDisabled(createMissingDisabled);
                    }
                }
            },
            createPublishedCheckbox,
            missingObjectPathTextfield
        ];
    }
});
