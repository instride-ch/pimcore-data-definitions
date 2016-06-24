pimcore.registerNS('pimcore.plugin.advancedimportexport.provider.csv');

pimcore.plugin.advancedimportexport.provider.csv = Class.create(pimcore.plugin.advancedimportexport.provider.abstractprovider, {
    getItems : function() {
        return [{
            xtype: 'textfield',
            name: 'delimiter',
            fieldLabel: t('advancedimportexport_csv_delimiter'),
            anchor : '100%',
            value: this.data['delimiter'] ? this.data.delimiter : ','
        },{
            xtype: 'textfield',
            name: 'enclosure',
            fieldLabel: t('advancedimportexport_csv_enclosure'),
            anchor : '100%',
            value: this.data['enclosure'] ? this.data.enclosure : '"'
        },{
            xtype : 'textarea',
            fieldLabel : t('advancedimportexport_csv_example'),
            name : 'csvExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['csvExample'] ? this.data.csvExample : ''
        }];
    }
});