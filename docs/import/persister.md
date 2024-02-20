## Persister
A persister takes care about the save process. It basically saves objects. Following Persisters are currently available:

- `Persister` saves objects

To create your own persister you need to implement `Instride\Bundle\DataDefinitionsBundle\Persister\PersisterInterface` 
and add a new service:

```yml
acme_bundle.data_definitions.my_persister:
    class: AcmeBundle\DataDefinitions\MyPersister
    tags:
      - { name: data_definitions.persister, type: my-persister }
```
