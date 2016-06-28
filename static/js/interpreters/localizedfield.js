pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters.localizedfield');

pimcore.plugin.advancedimportexport.interpreters.localizedfield = Class.create(pimcore.plugin.advancedimportexport.interpreters.abstract, {

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
