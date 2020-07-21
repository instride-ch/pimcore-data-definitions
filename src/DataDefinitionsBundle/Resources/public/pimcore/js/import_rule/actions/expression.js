/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.import_rule.actions.expression');
pimcore.plugin.datadefinitions.import_rule.actions.expression = Class.create(coreshop.rules.conditions.abstract, {

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
