pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.asset_by_path');

pimcore.plugin.datadefinitions.interpreters.asset_by_path = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
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
