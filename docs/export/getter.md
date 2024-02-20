## Getter
A Getter gets the data to the data object

You can also implement your own Getters.

Todo that, you need to implement the interface ```Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface``` and create a service

```yml
acme_bundle.data_definitions.my_getter:
    class: AcmeBundle\DataDefinitions\MyGetter
    tags:
      - { name: data_definitions.getter, type: mygetter }
```

If your Getter does have configuration as well, you need to create a new FormType and add a new Javascript file for the GUI:

```javascript
pimcore.registerNS('pimcore.plugin.datadefinitions.getters.mygetter');

pimcore.plugin.datadefinitions.getters.mygetter = Class.create(pimcore.plugin.datadefinitions.setters.abstract, {

});

```


You also need to load your Javascript File in your config.yml
```yml
data_definitionss:
  pimcore_admin:
    js:
      mygetter: '/static/pimcore/mygetter.js'
```
