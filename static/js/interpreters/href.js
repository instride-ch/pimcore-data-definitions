pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters.href');

pimcore.plugin.advancedimportexport.interpreters.href = Class.create(pimcore.plugin.advancedimportexport.interpreters.abstract, {

    getLayout : function (fromColumn, toColumn, record) {
        var classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/class/get-tree'
            },
            fields: ["text"]
        });
        classesStore.load();

        return [{
            xtype : 'combo',
            fieldLabel: t("class"),
            name: "class",
            displayField: "text",
            valueField: "text",
            store: classesStore,
            width: 500,
            value : record.data.config.class ? record.data.config.class : null
        }];
    }
});
