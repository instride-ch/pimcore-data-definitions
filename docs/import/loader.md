## Loader
A loader finds a Pimcore Object (or not) and returns it. Per Default, Data Definitions only comes with one Loader: PrimaryKey, which finds your DataObject based on un-interpreted Data based on what you select in the configuration.

To create your own Loader you need to implement ```Wvision\Bundle\DataDefinitionsBundle\Loader\LoaderInterface``` and add a new service

```yml
acme_bundle.data_definitions.my_loader:
    class: AcmeBundle\DataDefinitions\MyLoader
    tags:
      - { name: data_definitions.loader, type: my-loader }
```
