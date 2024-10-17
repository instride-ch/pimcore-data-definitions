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

pimcore.registerNS('pimcore.plugin.datadefinitions.provider.external_sql');

pimcore.plugin.datadefinitions.provider.external_sql = Class.create(pimcore.plugin.datadefinitions.provider.abstractprovider, {
    getItems: function () {
        return [{
            xtype: 'textfield',
            name: 'host',
            fieldLabel: t('data_definitions_sql_host'),
            anchor: '100%',
            value: this.data['host'] ? this.data.host : ''
        }, {
            xtype: 'textfield',
            name: 'username',
            fieldLabel: t('data_definitions_sql_username'),
            anchor: '100%',
            value: this.data['username'] ? this.data.username : ''
        }, {
            xtype: 'textfield',
            name: 'password',
            fieldLabel: t('data_definitions_sql_password'),
            anchor: '100%',
            value: this.data['password'] ? this.data.password : ''
        }, {
            xtype: 'textfield',
            name: 'database',
            fieldLabel: t('data_definitions_sql_database'),
            anchor: '100%',
            value: this.data['database'] ? this.data.database : ''
        }, {
            xtype: 'textfield',
            name: 'port',
            fieldLabel: t('data_definitions_sql_port'),
            anchor: '100%',
            value: this.data['port'] ? this.data.port : '3306'
        }, {
            xtype: 'textfield',
            name: 'adapter',
            fieldLabel: t('data_definitions_sql_adapter'),
            anchor: '100%',
            value: this.data['adapter'] ? this.data.adapter : 'pdo_mysql'
        }, {
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
