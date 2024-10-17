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

pimcore.registerNS('pimcore.plugin.datadefinitions.provider.csv');

pimcore.plugin.datadefinitions.provider.csv = Class.create(pimcore.plugin.datadefinitions.provider.abstractprovider, {
    getItems: function () {
        return [{
            xtype: 'textfield',
            name: 'delimiter',
            fieldLabel: t('data_definitions_csv_delimiter'),
            anchor: '100%',
            value: this.data['delimiter'] ? this.data.delimiter : ','
        }, {
            xtype: 'textfield',
            name: 'enclosure',
            fieldLabel: t('data_definitions_csv_enclosure'),
            anchor: '100%',
            value: this.data['enclosure'] ? this.data.enclosure : '"'
        }, {
            xtype: 'textarea',
            fieldLabel: t('data_definitions_csv_example'),
            name: 'csvExample',
            grow: true,
            anchor: '100%',
            minHeight: 300,
            value: this.data['csvExample'] ? this.data.csvExample : ''
        }, {
            xtype: 'container',
            html: '<strong>' + t('data_definitions_csv_headers_description') + '</strong>',
            padding: '0 0 0 200px'
        }, {
            xtype: 'textarea',
            fieldLabel: t('data_definitions_csv_headers'),
            name: 'csvHeaders',
            grow: true,
            anchor: '100%',
            minHeight: 300,
            value: this.data['csvHeaders'] ? this.data.csvHeaders : ''
        }];
    }
});
