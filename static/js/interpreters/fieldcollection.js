pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.fieldcollection');

pimcore.plugin.importdefinitions.interpreters.fieldcollection = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

    getLayout : function (fromColumn, toColumn, record) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'fieldcollectionField',
            length : 255,
            value : record.data.config.fieldcollectionField ? record.data.config.fieldcollectionField : null
        },{
            xtype : 'textfield',
            fieldLabel : t('importdefinitions_keys'),
            name : 'fieldcollectionKeys',
            length : 255,
            value : record.data.config.fieldcollectionKeys ? record.data.config.fieldcollectionKeys : null
        }];
    }
});
