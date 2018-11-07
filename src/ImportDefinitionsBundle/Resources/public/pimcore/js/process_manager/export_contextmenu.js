pimcore.registerNS("pimcore.plugin.importdefinitions.export.context_menu");

console.log('e');
pimcore.plugin.importdefinitions.export.context_menu = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.importdefinitions.export.context_menu";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    prepareObjectTreeContextMenu: function (tree, treeClass, menuRecord) {
        var object_types = pimcore.globalmanager.get("object_types_store_create");
        
        var objectMenu = {
            exporter: [],
            ref: this
        };

        var $this = this;

        object_types.each(function (classRecord) {
            objectMenu["exporter"].push({
                text: classRecord.get("translatedText"),
                iconCls: "pimcore_icon_object pimcore_icon_overlay_add",
                handler: $this.exportObjects.bind($this, classRecord, menuRecord)
            });
        });

        tree.add([
            { xtype: 'menuseparator' },
            {
                text: "Export Definitions",
                iconCls: "pimcore_icon_object pimcore_icon_overlay_download",
                menu: objectMenu.exporter
            }
        ]);
    },

    exportObjects: function (classRecord, itemRecord) {
        // TODO: run executable, passing item ID as "root" param
    }
});

new pimcore.plugin.importdefinitions.export.context_menu();
