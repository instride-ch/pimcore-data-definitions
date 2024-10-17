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

pimcore.registerNS('pimcore.plugin.datadefinitions.import_rule.actions.object');
pimcore.plugin.datadefinitions.import_rule.actions.object = Class.create(coreshop.rules.actions.abstract, {

    type: 'object',

    getForm: function () {
        this.object = Ext.create({
            xtype: 'textfield',
            fieldLabel: t('data_definitions_interpreter_object_id'),
            name: 'object',
            width: 500,
            value: this.data ? this.data.object : null
        });

        this.form = new Ext.form.Panel({
            items: [
                this.object
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            object: this.object.getValue()
        };
    }
});
