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

pimcore.registerNS('pimcore.plugin.datadefinitions.getters.fieldcollection');

pimcore.plugin.datadefinitions.getters.fieldcollection = Class.create(pimcore.plugin.datadefinitions.setters.fieldcollection, {
    getLayout: function (fromColumn, toColumn, record, config, definitionConfig) {
        this.fromColumn = fromColumn;

        this.fieldText = Ext.create({
            xtype: 'textfield',
            fieldLabel: t('field'),
            name: 'field',
            value: config.field ? config.field : null,
        });

        return [this.fieldText];
    },

    getGetterData: function () {
        return {
            'class': this.fromColumn.config.class,
            'field': this.fieldText.getValue(),
        };
    }
});
