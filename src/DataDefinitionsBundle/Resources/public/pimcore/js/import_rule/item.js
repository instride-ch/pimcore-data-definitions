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

pimcore.registerNS('pimcore.plugin.datadefinitions.import_rule.item');

pimcore.plugin.datadefinitions.import_rule.item = Class.create(coreshop.rules.item, {

    iconCls: 'data_definitions_icon_import_rules',

    initialize: function ($super, parentPanel, data, panelKey, type, record) {
        this.record = record;

        $super(parentPanel, data, panelKey, type);
    },

    getPanel: function () {
        var items = this.getItems();

        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            items: items
        });

        return this.panel;
    },

    getSettings: function () {
        var data = this.data;

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [
                {
                    xtype: 'textfield',
                    name: 'name',
                    fieldLabel: t('name'),
                    width: 250,
                    value: data.name
                },
                {
                    xtype: 'checkbox',
                    name: 'active',
                    fieldLabel: t('active'),
                    checked: data.active
                }
            ]
        });

        return this.settingsForm;
    },

    save: function (callback) {

    },

    getSaveData: function ($super) {
        saveData = $super();

        saveData.id = this.record.id;

        return saveData;
    },

    getActionContainerClass: function () {
        return pimcore.plugin.datadefinitions.import_rule.action;
    },

    getConditionContainerClass: function () {
        return pimcore.plugin.datadefinitions.import_rule.condition;
    }
});
