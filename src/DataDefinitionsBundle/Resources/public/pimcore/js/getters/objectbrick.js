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

pimcore.registerNS('pimcore.plugin.datadefinitions.getters.objectbrick');

pimcore.plugin.datadefinitions.getters.objectbrick = Class.create(pimcore.plugin.datadefinitions.setters.objectbrick, {
    getLayout: function (fromColumn, toColumn, record, config, definitionConfig) {
        this.fromColumn = fromColumn;

        var possibleFields = [];
        var fieldClass = fromColumn.config.class;

        Ext.Object.each(definitionConfig.bricks, function (key, value) {
            if (value.indexOf(fieldClass) >= 0) {
                possibleFields.push(key);
            }
        });

        this.fieldCombo = Ext.create({
            xtype: 'combo',
            fieldLabel: t('field'),
            name: 'brickField',
            value: config.brickField ? config.brickField : null,
            store: possibleFields,
            triggerAction: 'all',
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: 'local'
        });

        return [this.fieldCombo];
    },

    getGetterData: function () {
        return {
            'class': this.fromColumn.config.class,
            'brickField': this.fieldCombo.getValue()
        };
    }
});
