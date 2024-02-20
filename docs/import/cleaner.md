## Cleaner
A cleaner takes care about the clean-up process. It basically deletes or unpublishes the missing objects. Following Cleaners are currently available:

 - Deleter: Deletes missing objects
 - Unpublisher: Unpublishes missing objects
 - Reference Cleaner: Deletes only when no references exists, otherwise the object will be unpublished
 - None: does basically nothing

To create your own cleaner you need to implement ```Instride\Bundle\DataDefinitionsBundle\Cleaner\CleanerInterface``` and add a new service

```yml
acme_bundle.data_definitions.my_cleaner:
    class: AcmeBundle\DataDefinitions\MyCleaner
    tags:
      - { name: data_definitions.cleaner, type: my-cleaner }
```
