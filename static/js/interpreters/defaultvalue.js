pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters.defaultvalue');

pimcore.plugin.advancedimportexport.interpreters.defaultvalue = Class.create(pimcore.plugin.advancedimportexport.interpreters.abstract, {

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
