pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.asseturl');

pimcore.plugin.importdefinitions.interpreters.asseturl = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel: t("path"),
            name: "path",
            width: 500,
            value : config.path ? config.path : null
        }];
    }
});
