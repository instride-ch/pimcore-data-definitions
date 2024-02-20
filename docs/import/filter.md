## Filter
A Filter, as the name says, filters your data on runtime. Your method gets called on every "row" and you get to decide if you want to import it, or not.

To implement a new filter, you need to implement the interface ```Instride\Bundle\DataDefinitionsBundle\Filter\FilterInterface``` and add a new service

```yml
acme_bundle.data_definitions.my_filter:
    class: AcmeBundle\DataDefinitions\MyFilter
    tags:
      - { name: data_definitions.filter, type: my_filter }
```

```php
namespace AcmeBundle\DataDefinitions;

class MyFilter implements FilterInterface
{
    public function filter($definition, $data, $object) {
        if($data['isActive'])
        {
            return true;            //Will be imported
        }

        return false;               //Will be ignored
    }
}
```
