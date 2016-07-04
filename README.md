# Pimcore - Import Definitions

Import Definitions allows you to define your Object Import using a nice GUI and re-run the imports as often you like. Everything within Import Definitions is extendable.
Currently following providers are supported:

 * CSV
 * SQL-Table
 * JSON (only when max-depth = 1)

You can always programm a custom provider which takes care of imports. Just take a look at the existing providers.

![Interface](docs/mapping.png)

## Getting started

* Download Plugin and place it in your plugins directory
* Open Extension Manager in Pimcore and enable/install Plugin
* After Installation within Pimcore Extension Manager, you have to reload Pimcore
* Open Settings -> Definitions

## Providers
Currently, only 4 types of providers are available:

 - CSV
 - JSON
 - XML
 - SQL

Because, the data needs to be non-hierarchial, XML and JSON are very limited. You can write your own provider to prepare the data for the plugin. Todo that, you simply
need to create a new class within the "ImportDefinitions\Model\Provider" namespace and call

```
ImportDefinitions\Model\AbstractProvider::addProvider('YourProvider');
```

Take a look at the existing Providers to get a clue how they are working.

## Interpreter
To prepare data before it goes to the Objects-Setter Method, there are these "Interpreters". Currently following are available:

 - AssetsUrl -> Downloads an Asset from an Url and saves it to a multihref field
 - AssetUrl -> Downloads an Asset from an Url and saves it to a href field
 - Classificationstore -> Saves the data to a classificationstore field
 - DefaultValue -> Saves and Static-Value (definied in the UI) to the field
 - Href -> solves the connection from an ID to an actual Pimcore Objet
 - Localizedfield -> saves the data to the specific language field
 - MulitHref -> same as href, but for multihref fields
 - Objectbrick -> saves the data to an objectbrick

This probably doesn't satisfy your needs. But you can also write your own Interpreters. You just need to create a new class within the "ImportDefinitions\Model\Interpreter" namespace
and call

```
ImportDefinitions\Model\Interpreter::addInterpreter('YourInterpreter');
```

if you have to add some data within the UI, you also need to create a Pimcore Admin JS File:

```
pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.yourinterpreter');

pimcore.plugin.importdefinitions.interpreters.yourinterpreter = Class.create(pimcore.plugin.importdefinitions.interpreters.abstract, {

});

```

## List your Definitions (in CLI)

Run following command

```
pimcore/cli/console.php importdefinitions:list
```

You can see the ID, the name and the Provider

## Run your definition
Definitions can only run (at the moment) using the Pimcore CLI. To run your definition, use following command

```
pimcore/cli/console.php importdefinitions:run -d 1 -p "{\"file\":\"test.json\"}"
```

## Copyright and license 
Copyright: [Woche Pass AG](http://www.w-vision.ch)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![Interface](docs/settings.png)
![Interface](docs/provider-settings.png)
