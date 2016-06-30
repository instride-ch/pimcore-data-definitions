pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.classificationstore');

pimcore.plugin.importdefinitions.interpreters.classificationstore = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

    getLayout : function (fromColumn, toColumn, record) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'classificationstoreField',
            length : 255,
            value : record.data.config.classificationstoreField ? record.data.config.classificationstoreField : null
        }];
    }
});
