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
