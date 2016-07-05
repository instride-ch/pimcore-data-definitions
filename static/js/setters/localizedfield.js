pimcore.registerNS('pimcore.plugin.importdefinitions.setters.localizedfield');

pimcore.plugin.importdefinitions.setters.localizedfield = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel: t("language"),
            name: "language",
            width: 500,
            value : config.language ? config.language : null
        }];
    }
});
