/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.setters.fieldcollection');

pimcore.plugin.datadefinitions.setters.fieldcollection = Class.create(pimcore.plugin.datadefinitions.setters.abstract, {
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
            fieldLabel: t('data_definitions_keys'),
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
