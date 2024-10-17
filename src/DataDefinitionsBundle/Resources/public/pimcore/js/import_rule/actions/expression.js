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

pimcore.registerNS('pimcore.plugin.datadefinitions.import_rule.actions.expression');
pimcore.plugin.datadefinitions.import_rule.actions.expression = Class.create(coreshop.rules.actions.abstract, {

    type: 'expression',

    getForm: function () {
        this.expression = Ext.create({
            xtype: 'textfield',
            fieldLabel: t('data_definitions_interpreter_expression'),
            name: 'expression',
            width: 500,
            value: this.data ? this.data.expression : null
        });

        this.form = new Ext.form.Panel({
            items: [
                this.expression
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            expression: this.expression.getValue()
        };
    }
});
