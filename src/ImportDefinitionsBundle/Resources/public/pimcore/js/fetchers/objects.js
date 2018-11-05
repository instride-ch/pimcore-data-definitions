/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions.fetchers.definition');

pimcore.plugin.importdefinitions.fetchers.objects = Class.create(pimcore.plugin.importdefinitions.fetchers.abstract, {
    getLayout : function (data, config) {
        return [
            {
                xtype : 'checkbox',
                fieldLabel: t('importdefinitions_fetcher_objects_unpublished'),
                name: 'unpublished',
                value : Ext.isDefined(data.unpublished) ? data.unpublished : false
            }
        ];
    }
});
