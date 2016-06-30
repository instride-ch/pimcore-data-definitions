pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.objectbrick');

pimcore.plugin.importdefinitions.interpreters.objectbrick = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

    getLayout : function (fromColumn, toColumn, record) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'brickField',
            length : 255,
            allowBlank : false,
            value : record.data.config.brickField ? record.data.config.brickField : null
        }];
    }
});
