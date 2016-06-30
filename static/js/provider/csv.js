pimcore.registerNS('pimcore.plugin.importdefinitions.provider.csv');

pimcore.plugin.importdefinitions.provider.csv = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function() {
        return [{
            xtype: 'textfield',
            name: 'delimiter',
            fieldLabel: t('importdefinitions_csv_delimiter'),
            anchor : '100%',
            value: this.data['delimiter'] ? this.data.delimiter : ','
        },{
            xtype: 'textfield',
            name: 'enclosure',
            fieldLabel: t('importdefinitions_csv_enclosure'),
            anchor : '100%',
            value: this.data['enclosure'] ? this.data.enclosure : '"'
        },{
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_csv_example'),
            name : 'csvExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['csvExample'] ? this.data.csvExample : ''
        }];
    }
});