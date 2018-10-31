Ext.define('ImportDefinitions.resource.Definition', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.import_definition.definition',

    name: 'country',
    fieldLabel: t('importdefinitions_definition'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('importdefinitions_definitions');

        this.callParent();
    }
});