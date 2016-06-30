pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters');
pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.abstract');

pimcore.plugin.importdefinitions.interpreters.abstract = Class.create({

    getLayout : function () {
        return [];
    }

});
