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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.import_rule');

pimcore.plugin.datadefinitions.interpreters.import_rule = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    rules: null,
    window: null,

    getLayout: function (fromColumn, toColumn, record, config) {
        var me = this;
        me.rules = config.rules;

        return Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            items: [
                {
                    xtype: 'button',
                    text: t('edit'),
                    handler: function () {
                        me.panel = new pimcore.plugin.datadefinitions.import_rule.panel(me, me.rules, me.getActions(), me.getConditions());

                        me.window = new Ext.Window({
                            width: '80%',
                            height: '80%',
                            resizeable: true,
                            modal: false,
                            closeable: false,
                            title: t('data_definitions_import_rules'),
                            iconCls: 'data_definitions_icon_import_rules',
                            layout: 'fit',
                            items: me.panel.getLayout()
                        }).show();
                    }
                }
            ],
            border: false
        });
    },

    close: function (rules) {
        this.rules = rules;
        this.window.destroy();
    },

    getActions: function () {
        return pimcore.globalmanager.get('data_definitions_import_rule_actions');
    },

    getConditions: function () {
        return pimcore.globalmanager.get('data_definitions_import_rule_conditions');
    },

    getInterpreterData: function () {
        // get defined conditions
        return {
            rules: this.rules
        };
    }
});
