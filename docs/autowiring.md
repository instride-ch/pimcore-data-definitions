## Auto Wiring
Data-Definitions also does auto-wire your Services, so, if you don't have any special configurations for your extensions, you can simply do this and your extensions get registered without configuration automatically:

```yml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

    AgroBundle\DataDefinitions\:
        resource: '../../DataDefinitions'
```

