Ext.define('ImportDefinitions.resource.Definition', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.import_definition.definition',

    name: 'country',
    fieldLabel: t('data_definitions_definition'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('data_definitions_definitions');

        this.callParent();
    }
});
