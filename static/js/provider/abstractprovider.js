pimcore.registerNS('pimcore.plugin.advancedimportexport.provider.abstractprovider');

pimcore.plugin.advancedimportexport.provider.abstractprovider = Class.create({

    data : {},

    initialize: function (data) {
        this.data = data;
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
                items: this.getItems()
            });
        }

        return this.form;
    },

    getItems : function() {
        return [];
    }
});