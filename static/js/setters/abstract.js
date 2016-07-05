pimcore.registerNS('pimcore.plugin.importdefinitions.setters');
pimcore.registerNS('pimcore.plugin.importdefinitions.setters.abstract');

pimcore.plugin.importdefinitions.setters.abstract = Class.create({

    getLayout : function () {
        return [];
    }

});
