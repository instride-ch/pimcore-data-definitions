pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters');
pimcore.registerNS('pimcore.plugin.advancedimportexport.interpreters.abstract');

pimcore.plugin.advancedimportexport.interpreters.abstract = Class.create({

    getLayout : function () {
        return [];
    }

});
