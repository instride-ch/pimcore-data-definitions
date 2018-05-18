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

pimcore.registerNS('pimcore.plugin.importdefinitions.provider.csv');

pimcore.plugin.importdefinitions.provider.csv = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function () {
        return [{
            xtype: 'textfield',
            name: 'delimiter',
            fieldLabel: t('importdefinitions_csv_delimiter'),
            anchor : '100%',
            value: this.data['delimiter'] ? this.data.delimiter : ','
        }, {
            xtype: 'textfield',
            name: 'enclosure',
            fieldLabel: t('importdefinitions_csv_enclosure'),
            anchor : '100%',
            value: this.data['enclosure'] ? this.data.enclosure : '"'
        }, {
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_csv_example'),
            name : 'csvExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['csvExample'] ? this.data.csvExample : ''
        }, {
            xtype: 'container',
            html: '<strong>' + t('importdefinitions_csv_headers_description') + '</strong>',
            padding: '0 0 0 200px'
        }, {
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_csv_headers'),
            name : 'csvHeaders',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['csvHeaders'] ? this.data.csvHeaders : ''
        }];
    }
});
