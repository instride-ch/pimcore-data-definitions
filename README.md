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

## List your Definitions (in CLI)

Run following command

```
pimcore/cli/console.php importdefinitions:list -d 1 -p "{\"file\":\"test.json\"}"
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
