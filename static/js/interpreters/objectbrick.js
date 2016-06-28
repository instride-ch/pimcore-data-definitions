pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters.objectbrick');

pimcore.plugin.advancedimportexport.interpreters.objectbrick = Class.create(pimcore.plugin.advancedimportexport.interpreters.abstract, {

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
