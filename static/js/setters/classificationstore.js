pimcore.registerNS('pimcore.plugin.importdefinitions.setters.classificationstore');

pimcore.plugin.importdefinitions.setters.classificationstore = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'classificationstoreField',
            length : 255,
            value : config.classificationstoreField ? config.classificationstoreField : null
        }];
    }
});
