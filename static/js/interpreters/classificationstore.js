pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters.classificationstore');

pimcore.plugin.advancedimportexport.interpreters.classificationstore = Class.create(pimcore.plugin.advancedimportexport.interpreters.abstract, {

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
