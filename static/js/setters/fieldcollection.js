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

pimcore.registerNS('pimcore.plugin.importdefinitions.setters.fieldcollection');

pimcore.plugin.importdefinitions.setters.fieldcollection = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'fieldcollectionField',
            length : 255,
            value : config.fieldcollectionField ? config.fieldcollectionField : null
        }, {
            xtype : 'textfield',
            fieldLabel : t('importdefinitions_keys'),
            name : 'fieldcollectionKeys',
            length : 255,
            value : config.fieldcollectionKeys ? config.fieldcollectionKeys : null
        }];
    }
});
