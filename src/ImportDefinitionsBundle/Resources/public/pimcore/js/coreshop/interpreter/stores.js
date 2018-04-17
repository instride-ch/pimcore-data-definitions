pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.coreshop_stores');
pimcore.plugin.importdefinitions.interpreters.coreshop_stores = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

    getLayout: function (fromColumn, toColumn, record, config) {
        return [{
            xtype: 'coreshop.store',
            name: 'stores',
            multiSelect: true,
            typeAhead: false,
            value: config ? config.stores : []
        }];
    }
});
