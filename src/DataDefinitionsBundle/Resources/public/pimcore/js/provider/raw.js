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

pimcore.registerNS('pimcore.plugin.datadefinitions.provider.raw');

pimcore.plugin.datadefinitions.provider.raw = Class.create(pimcore.plugin.datadefinitions.provider.abstractprovider, {
    getItems: function () {
        return [{
            xtype: 'textarea',
            fieldLabel: t('data_definitions_data_object_headers'),
            name: 'headers',
            grow: true,
            anchor: '100%',
            minHeight: 300,
            value: this.data['headers'] ? this.data.headers : ''
        }];
    }
});
