pimcore.registerNS('pimcore.plugin.importdefinitions.setters.objectbrick');

pimcore.plugin.importdefinitions.setters.objectbrick = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'brickField',
            length : 255,
            allowBlank : false,
            value : config.brickField ? config.brickField : null
        }];
    }
});
