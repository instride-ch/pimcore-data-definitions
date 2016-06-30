pimcore.registerNS('pimcore.plugin.importdefinitions.provider.json');

pimcore.plugin.importdefinitions.provider.json = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function() {
        return [{
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_json_example'),
            name : 'jsonExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['jsonExample'] ? this.data.jsonExample : ''
        }];
    }
});