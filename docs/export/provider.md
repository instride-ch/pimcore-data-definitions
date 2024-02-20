## Export Providers
Currently, these Export Provider Types are supported:

 - CSV
 - JSON
 - XML
 
To create a custom provider, you need to implement ```Instride\Bundle\DataDefinitionsBundle\Provider\ExportProviderInterface``` namespace and add a new service:

```yml
acme_bundle.data_definitions.provider.my_provider:
    class: AcmeBundle\DataDefinitions\MyProvider
    tags:
      - { name: data_definitions.export_provider, type: my_provider, form-type: AcmeBundle\Form\Type\MyProviderType }
```

Take a look at the existing Providers to get a clue how they are working.
