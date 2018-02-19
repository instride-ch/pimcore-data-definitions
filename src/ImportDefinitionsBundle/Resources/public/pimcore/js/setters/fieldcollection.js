/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions.setters.fieldcollection');

pimcore.plugin.importdefinitions.setters.fieldcollection = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout: function (fromColumn, toColumn, record, config, definitionConfig) {
        this.toColumn = toColumn;

        var possibleFields = [];
        var fieldClass = toColumn.data.config.class;

        Ext.Object.each(definitionConfig.fieldcollections, function (key, value) {
            if (value.indexOf(fieldClass) >= 0) {
                possibleFields.push(key);
            }
        });

        this.fieldCombo = Ext.create({
            xtype: 'combo',
            fieldLabel: t('field'),
            name: 'field',
            value: config.field ? config.field : null,
            store: possibleFields,
            triggerAction: 'all',
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: 'local'
        });

        this.keysField = Ext.create({
            xtype: 'textfield',
            fieldLabel: t('importdefinitions_keys'),
            name: 'keys',
            length: 255,
            value: config.keys ? config.keys : null
        });

        return [this.fieldCombo, this.keysField];
    },

    getSetterData: function () {
        return {
            'class': this.toColumn.data.config.class,
            'field': this.fieldCombo.getValue(),
            'keys': this.keysField.getValue()
        };
    }
});
