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

pimcore.registerNS('pimcore.plugin.datadefinitions.setters.coreshop_store_values');
pimcore.registerNS('pimcore.plugin.importdefinitions.setters.coreshop_store_values');

pimcore.plugin.datadefinitions.setters.coreshop_store_values = Class.create(pimcore.plugin.datadefinitions.setters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype: 'coreshop.store',
            name: 'stores',
            multiSelect: true,
            typeAhead: false,
            value: config ? config.stores : []
        }, {
            xtype: 'textfield',
            name: 'type',
            fieldLabel: t('type'),
            value: config ? config.type : []
        }];
    }
});
pimcore.plugin.importdefinitions.setters.coreshop_store_values = Class.create(pimcore.plugin.datadefinitions.setters.coreshop_store_values, {});
