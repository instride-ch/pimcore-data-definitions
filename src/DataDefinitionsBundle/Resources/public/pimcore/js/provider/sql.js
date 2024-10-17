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

pimcore.registerNS('pimcore.plugin.datadefinitions.provider.sql');

pimcore.plugin.datadefinitions.provider.sql = Class.create(pimcore.plugin.datadefinitions.provider.abstractprovider, {
    getItems: function () {
        return [{
            xtype: 'textarea',
            fieldLabel: t('data_definitions_sql_query'),
            name: 'query',
            grow: true,
            anchor: '100%',
            minHeight: 300,
            value: this.data['query'] ? this.data.query : ''
        }];
    }
});
