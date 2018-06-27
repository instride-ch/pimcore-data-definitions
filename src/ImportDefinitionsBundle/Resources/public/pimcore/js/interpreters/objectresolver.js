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
                value : Ext.isDefined(config.match_unpublished) ? config.match_unpublished : true
            }];
    }
});
