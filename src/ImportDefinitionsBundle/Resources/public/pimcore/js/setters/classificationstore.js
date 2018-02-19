/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.importdefinitions.setters.classification_store');

pimcore.plugin.importdefinitions.setters.classification_store = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout : function (fromColumn, toColumn, record, config, definitionConfig) {
        this.toColumn = toColumn;

        this.classificationStoreField = Ext.create({
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'classificationstoreField',
            length : 255,
            value : config.classificationstoreField ? config.classificationstoreField : null
        });

        return [this.classificationStoreField];
    },

    getSetterData: function () {
        return {
            'keyId': this.toColumn.data.config.keyId,
            'groupId': this.toColumn.data.config.groupId,
            'classificationstoreField': this.classificationStoreField.getValue()
        };
    }
});
