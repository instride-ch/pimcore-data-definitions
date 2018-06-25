pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.asset_by_path');

pimcore.plugin.importdefinitions.interpreters.asset_by_path = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
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
