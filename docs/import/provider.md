## Import Providers
Currently, these Import Provider Types are supported:

 - CSV
 - JSON
 - XML
 - SQL
 - External SQL
 - Raw (for nested imports)

Because, the data needs to be non-hierarchial, XML and JSON are very limited. You can write your own provider to prepare the data for the plugin. To do that, you simply
need to create a new class and implement ```Instride\Bundle\DataDefinitionsBundle\Provider\ImportProviderInterface``` namespace and add a new service:

```yml
acme_bundle.data_definitions.provider.my_provider:
    class: AcmeBundle\DataDefinitions\MyProvider
    tags:
      - { name: data_definitions.import_provider, type: my_provider, form-type: AcmeBundle\Form\Type\MyProviderType }
```

Take a look at the existing Providers to get a clue how they are working.
