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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.metadata');

pimcore.plugin.datadefinitions.interpreters.metadata = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        return [{
            xtype: 'combo',
            fieldLabel: t('class'),
            name: 'class',
            value: config.class ? config.class : null,
            store: ['ElementMetadata', 'ObjectMetadata'],
        }, {
            xtype: 'textfield',
            fieldLabel: t('metadata'),
            name: 'metadata',
            width: 500,
            value: config.metadata ? config.metadata : null
        }];
    }
});
