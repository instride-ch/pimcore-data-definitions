/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions.provider.abstractprovider');

pimcore.plugin.importdefinitions.provider.abstractprovider = Class.create({
    data : {},
    parentItemPanel : null,

    initialize: function (data, parentItemPanel) {
        this.data = data;
        this.parentItemPanel = parentItemPanel;
    },

    getForm : function () {
        if (!this.form) {
            this.form = new Ext.form.Panel({
                bodyStyle: 'padding:10px;',
                region : 'center',
                autoScroll: true,
                defaults: {
                    labelWidth: 200
                },
                border: false,
                items: this.getItems(),
                buttons: [{
                    text: t('test'),
                    iconCls: 'pimcore_icon_apply',
                    handler: this.test.bind(this)
                }],
            });
        }

        return this.form;
    },

    getItems : function () {
        return [];
    },

    test : function () {
        this.parentItemPanel.save(function () {
            Ext.Ajax.request({
                url: this.parentItemPanel.url.test,
                method: 'get',
                params: {
                    id : this.parentItemPanel.data.id
                },
                success: function (response) {
                    try {
                        var res = Ext.decode(response.responseText);

                        if (res.success) {
                            pimcore.helpers.showNotification(t('success'), t('success'), 'success');

                            this.parentItemPanel.providerSettingsSuccess(this);
                        } else {
                            pimcore.helpers.showNotification(t('error'), res.message, 'error');
                        }
                    } catch (e) {
                        pimcore.helpers.showNotification(t('error'), t('error'), 'error');
                    }
                }.bind(this)
            });
        }.bind(this));
    }
});
