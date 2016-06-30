pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.localizedfield');

pimcore.plugin.importdefinitions.interpreters.localizedfield = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

    getLayout : function (fromColumn, toColumn, record) {
        return [{
            xtype : 'textfield',
            fieldLabel: t("language"),
            name: "language",
            width: 500,
            value : record.data.config.language ? record.data.config.language : null
        }];
    }
});
