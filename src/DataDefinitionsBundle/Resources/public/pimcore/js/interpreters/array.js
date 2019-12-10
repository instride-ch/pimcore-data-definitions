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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.array');

pimcore.plugin.datadefinitions.interpreters.array = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        var csvSeparatorField = Ext.create({
            xtype: 'textfield',
            fieldLabel: t('separator'),
            name: 'csv_separator',
            width: 500,
            hidden: config.type != 'CSV',
            value: config.csv_separator ? config.csv_separator : ','
        });

        var typeCombo = Ext.create({
            xtype: 'combo',
            fieldLabel: t('type'),
            name: 'type',
            value: config.type ? config.type : null,
            store: [
                ['json', 'Json'],
                ['serialized', 'PHP Serialized'],
                ['csv', 'CSV']
            ],
            listeners: {
                change: function (o, newValue, oldValue, eOpts) {
                    if (newValue == 'csv') {
                        csvSeparatorField.setVisible(true);
                    } else {
                        csvSeparatorField.setVisible(false);
                    }
                }
            }
        });

        return [typeCombo, csvSeparatorField];
    }
});
