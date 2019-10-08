pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.coreshop_money');

pimcore.plugin.datadefinitions.interpreters.coreshop_money = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout: function (fromColumn, toColumn, record, config) {
        return [
            {
                xtype: 'checkbox',
                fieldLabel: t('data_definitions_interpreter_coreshop_is_float'),
                name: 'isFloat',
                value: config.isFloat ? config.isFloat : false
            },
            {
                xtype: 'coreshop.currency',
                name: 'currency',
                multiSelect: false,
                typeAhead: false,
                value: config.currency
            }
        ];
    }
});
