pimcore.registerNS('pimcore.plugin.importdefinitions.setters.fieldcollection');

pimcore.plugin.importdefinitions.setters.fieldcollection = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'fieldcollectionField',
            length : 255,
            value : config.fieldcollectionField ? config.fieldcollectionField : null
        },{
            xtype : 'textfield',
            fieldLabel : t('importdefinitions_keys'),
            name : 'fieldcollectionKeys',
            length : 255,
            value : config.fieldcollectionKeys ? config.fieldcollectionKeys : null
        }];
    }
});
