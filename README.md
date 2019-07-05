![ImportDefinitions](docs/images/github_banner.png "Data Definitions")

**Looking for the current stable [version 1](https://github.com/w-vision/ImportDefinitions/tree/1.2)?**

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Stable Version](https://img.shields.io/packagist/v/w-vision/import-definitions.svg?style=flat-square)](https://packagist.org/packages/w-vision/import-definitions)


Data Definitions allows you to define your DataObject Imports and Exports using a nice GUI and re-run the imports as often you like. Everything within Data Definitions is extendable.

![Interface](docs/images/mapping.png)

## Requirements
* Pimcore 5.8 or 6.0

## Getting started/Installation

 * Install via composer ```composer require w-vision/import-definitions```
 * Enable via command-line (or inside the pimcore extension manager): ```bin/console pimcore:bundle:enable DataDefinitionsBundle```
 * Install via command-line (or inside the pimcore extension manager): ```bin/console pimcore:bundle:install DataDefinitionsBundle```
 * Reload Pimcore
 * Open Settings -> Import Definitions or Export Definitions

## Docs
 - [Import Definitions](./docs/imports.md)
 - [Export Definitions](./docs/exports.md)

## License
[w-vision AG](https://www.w-vision.ch), Sandgruebestrasse 4, 6210 Sursee, Switzerland  
https://www.w-vision.ch, support@w-vision.ch  
Copyright © 2018 w-vision AG. All rights reserved.

For licensing details please visit [LICENSE.md](LICENSE.md) 
