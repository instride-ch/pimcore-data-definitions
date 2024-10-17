/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

document.addEventListener('processmanager.ready', function () {
    processmanager.executable.types.exportdefinition = Class.create(pimcore.plugin.processmanager.executable.abstractType, {
        getItems: function () {
            pimcore.globalmanager.get('data_definitions_export_definitions').load();

            return [{
                xtype: 'combo',
                fieldLabel: t('data_definitions_export_definitions'),
                name: 'definition',
                displayField: 'name',
                valueField: 'id',
                store: pimcore.globalmanager.get('data_definitions_export_definitions'),
                value: this.data.settings.definition,
                allowBlank: false
            }, {
                xtype: 'fieldcontainer',
                fieldLabel: t('data_definitions_processmanager_params'),
                layout: 'hbox',
                width: 500,
                value: this.data.settings.filePath,
                items: [{
                    xtype: "textfield",
                    name: 'params',
                    id: 'data_definitions_processmanager_params',
                    width: 450,
                    value: this.data.settings.params,
                    allowBlank: true
                }]
            }];
        }
    });
});
