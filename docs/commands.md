## List your Import Definitions (in CLI)

Run following command

```cli
bin/console data-definitions:list:imports
```

## List your Export Definitions (in CLI)

Run following command

```cli
bin/console data-definitions:list:exports
```

You can see the ID, the name and the Provider

## Run your Import Definition
Import Definitions can only run using the Pimcore CLI. To run your definition, use following command

```cli
bin/console import-definitions:import -d 1 -p "{\"file\":\"test.json\"}"
bin/console import-definitions:import -d name-of-definition -p "{\"file\":\"test.json\"}"
```

## Run your Export Definition
Export Definitions can only run (at the moment) using the Pimcore CLI. To run your definition, use following command

```cli
bin/console data-definitions:export -d 1 -p "{\"file\":\"test.json\"}"
bin/console data-definitions:export -d name-of-definition -p "{\"file\":\"test.json\"}"
```
