pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.asset');

pimcore.plugin.importdefinitions.interpreters.asset = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel: t('path'),
            name: 'path',
            width: 500,
            value : config.path || null
        }];
    }
});
