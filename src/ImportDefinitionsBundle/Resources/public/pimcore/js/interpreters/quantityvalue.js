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

pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.quantity_value');

pimcore.plugin.importdefinitions.interpreters.quantity_value = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'combo',
            fieldLabel: t('quantityValue'),
            name: 'unit',
            displayField: 'abbreviation',
            valueField: 'id',
            store: pimcore.helpers.quantityValue.getClassDefinitionStore(),
            width: 500,
            value : config.unit ? config.unit : null
        }];
    }
});
