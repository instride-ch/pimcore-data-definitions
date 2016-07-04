pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.asseturl');

pimcore.plugin.importdefinitions.interpreters.asseturl = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record) {
        return [{
            xtype : 'textfield',
            fieldLabel: t("path"),
            name: "path",
            width: 500,
            value : record.data.config.path ? record.data.config.path : null
        }];
    }
});
