pimcore.registerNS('pimcore.plugin.importdefinitions.provider.xml');

pimcore.plugin.importdefinitions.provider.xml = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function() {
        return [{
            xtype: 'textfield',
            name: 'rootNode',
            fieldLabel: t('importdefinitions_xml_rootNode'),
            anchor : '100%',
            value: this.data['rootNode'] ? this.data.rootNode : ''
        },{
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_xml_example'),
            name : 'xmlExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['xmlExample'] ? this.data.xmlExample : ''
        }];
    }
});