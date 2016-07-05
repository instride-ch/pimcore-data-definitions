/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions.provider.xml');

pimcore.plugin.importdefinitions.provider.xml = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function () {
        return [{
            xtype: 'textfield',
            name: 'rootNode',
            fieldLabel: t('importdefinitions_xml_rootNode'),
            anchor : '100%',
            value: this.data['rootNode'] ? this.data.rootNode : ''
        }, {
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_xml_example'),
            name : 'xmlExample',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['xmlExample'] ? this.data.xmlExample : ''
        }];
    }
});
