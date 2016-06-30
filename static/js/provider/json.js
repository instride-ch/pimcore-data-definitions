pimcore.registerNS('pimcore.plugin.advancedimportexport.provider.json');

pimcore.plugin.advancedimportexport.provider.json = Class.create(pimcore.plugin.advancedimportexport.provider.abstractprovider, {
    getItems : function() {
        return [{
            xtype : 'textarea',
            fieldLabel : t('advancedimportexport_json_example'),
            name : 'jsonExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['jsonExample'] ? this.data.jsonExample : ''
        }];
    }
});