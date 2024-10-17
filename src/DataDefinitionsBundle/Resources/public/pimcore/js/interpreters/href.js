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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.href');

pimcore.plugin.datadefinitions.interpreters.href = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        var typeStore = new Ext.data.ArrayStore({
            fields: ['key', 'value'],
            data: [
                ['asset', t('asset')],
                ['object', t('object')],
                ['document', t('document')]
            ]
        });
        var classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/class/get-tree'
            },
            fields: ['text']
        });
        classesStore.load();

        return [
            {
                xtype: 'combo',
                fieldLabel: t('element'),
                name: 'type',
                displayField: 'value',
                valueField: 'key',
                store: typeStore,
                width: 500,
                value: config.type ? config.type : 'object'
            },
            {
                xtype: 'combo',
                fieldLabel: t('class'),
                name: 'class',
                displayField: 'text',
                valueField: 'text',
                store: classesStore,
                width: 500,
                value: config.class ? config.class : null
            }
        ];
    }
});
