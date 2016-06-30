pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.defaultvalue');

pimcore.plugin.importdefinitions.interpreters.defaultvalue = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

    getLayout : function (fromColumn, toColumn, record) {
        return [{
            xtype : 'textfield',
            fieldLabel: t("value"),
            name: "value",
            width: 500,
            value : record.data.config.value ? record.data.config.value : null
        }];
    }
});
