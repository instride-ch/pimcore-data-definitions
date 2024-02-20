/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
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
