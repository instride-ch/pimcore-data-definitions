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

pimcore.registerNS('pimcore.plugin.datadefinitions.fetchers.definition');

pimcore.plugin.datadefinitions.fetchers.objects = Class.create(pimcore.plugin.datadefinitions.fetchers.abstract, {
    getLayout: function (data, config) {
        return [
            {
                xtype: 'checkbox',
                fieldLabel: t('data_definitions_fetcher_objects_unpublished'),
                name: 'unpublished',
                value: Ext.isDefined(data.unpublished) ? data.unpublished : false
            }
        ];
    }
});
