pimcore.registerNS('pimcore.plugin.importdefinitions.provider.abstractprovider');

pimcore.plugin.importdefinitions.provider.abstractprovider = Class.create({

    data : {},
    parentItemPanel : null,

    initialize: function (data, parentItemPanel) {
        this.data = data;
        this.parentItemPanel = parentItemPanel;
    },

    getForm : function() {
        if(!this.form) {
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

    getItems : function() {
        return [];
    },

    test : function() {
        this.parentItemPanel.save(function() {
            Ext.Ajax.request({
                url: this.parentItemPanel.url.test,
                method: 'post',
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