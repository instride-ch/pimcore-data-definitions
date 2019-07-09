/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.export_provider.csv');

pimcore.plugin.datadefinitions.export_provider.csv = Class.create(pimcore.plugin.datadefinitions.export_provider.abstractprovider, {
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
        }];
    }
});
