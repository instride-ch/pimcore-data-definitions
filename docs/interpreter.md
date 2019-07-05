## Interpreter
To prepare data before it goes to the Data Objects or your Export File, there are these "Interpreters"

Data Definitions provides you with a basic starting set of Interpreters, but you can create custom ones as well.

Todo that, you need to implement the interface ```Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface``` and create a service

```yml
acme_bundle.data_definitions.my_interpter:
    class: AcmeBundle\DataDefinitions\MyInterpreter
    tags:
      - { name: data_definitions.interpreter, type: myinterpreter, form-type: Wvision\Bundle\DataDefinitionsBundle\Form\Type\Interpreter\NoConfigurationType }
```

If your Interpter does have configuration as well, you need to create a new FormType and add a new Javascript file for the GUI:

```javascript
pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.myinterpreter');

pimcore.plugin.datadefinitions.interpreters.myinterpreter = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {

});

```

You also need to load your Javascript File in your config.yml

```yml
data_definitionss:
  pimcore_admin:
    js:
      my_interpter: '/static/pimcore/myinterpter.js'
```
