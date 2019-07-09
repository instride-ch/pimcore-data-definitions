## Fetcher
A fetcher finds Pimcore Objects and returns it. Per Default, Data Definitions only comes with one Fetcher: ObjectsFetcher, which returns all Objects of a Type.

To create your own Loader you need to implement ```Wvision\Bundle\DataDefinitionsBundle\Fetcher\FetcherInterface``` and add a new service

```yml
acme_bundle.data_definitions.my_fetcher:
    class: AcmeBundle\DataDefinitions\MyFetcher
    tags:
      - { name: data_definitions.fetcher, type: my-fetcher }
```
