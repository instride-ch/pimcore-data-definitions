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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.type_casting');

pimcore.plugin.datadefinitions.interpreters.type_casting = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        const typeStore = new Ext.data.ArrayStore({
            fields: ['key', 'value'],
            data: [
                ['int', 'integer'],
                ['float', 'float'],
                ['string', 'string'],
                ['boolean', 'boolean']
            ]
        });

        return [
            {
                xtype: 'combo',
                fieldLabel: t('type'),
                name: 'toType',
                displayField: 'value',
                valueField: 'key',
                store: typeStore,
                width: 500,
                value: config.Totype ? config.Totype : 'int'
            },
        ];
    }
});
