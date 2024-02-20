## Runner
A runner gets called before and after every line is imported from your data-source or exported to your export target. This can help you do clean-up or similar stuff.

To implement a new Runner, you need to implement the interface ```Instride\Bundle\DataDefinitionsBundle\Runner\RunnerInterface``` and add a new service

```yml
acme_bundle.data_definitions.my_runner:
    class: AcmeBundle\DataDefinitions\MyRunner
    tags:
      - { name: data_definitions.runner, type: my_runner }
```

```php
namespace AcmeBundle\DataDefinitions;

class MyRunner implements RunnerInterface
{
    public function preRun(Concrete $object, $data, DefinitionInterface $definition, $params) {
        //gets called before the row gets imported
    }

    public function postRun(Concrete $object, $data, DefinitionInterface $definition, $params) {
        //gets called after the row was imported
    }
}
```
