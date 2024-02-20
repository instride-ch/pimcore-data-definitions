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
