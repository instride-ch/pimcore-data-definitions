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
