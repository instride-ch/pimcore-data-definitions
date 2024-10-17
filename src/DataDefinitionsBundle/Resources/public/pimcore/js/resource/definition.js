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

Ext.define('DataDefinitions.resource.Definition', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.data_definitions.import_definition',

    name: 'country',
    fieldLabel: t('data_definitions_definition'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('data_definitions_definitions');

        this.callParent();
    }
});
