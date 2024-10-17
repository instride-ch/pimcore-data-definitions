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

pimcore.registerNS('pimcore.plugin.datadefinitions.export_provider.abstractprovider');

pimcore.plugin.datadefinitions.export_provider.abstractprovider = Class.create({
    data: {},
    parentItemPanel: null,

    initialize: function (data, parentItemPanel) {
        this.data = data;
        this.parentItemPanel = parentItemPanel;
    },

    getForm: function () {
        if (!this.form) {
            this.form = new Ext.form.Panel({
                bodyStyle: 'padding:10px;',
                region: 'center',
                autoScroll: true,
                defaults: {
                    labelWidth: 200
                },
                border: false,
                items: this.getItems()
            });
        }

        return this.form;
    },

    getItems: function () {
        return [];
    }
});
