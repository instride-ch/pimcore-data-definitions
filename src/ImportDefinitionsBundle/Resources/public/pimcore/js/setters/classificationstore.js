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

pimcore.registerNS('pimcore.plugin.importdefinitions.setters.classificationstore');

pimcore.plugin.importdefinitions.setters.classificationstore = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        this.toColumn = toColumn;

        this.classificationStoreField = Ext.create({
            xtype : 'textfield',
            fieldLabel : t('field'),
            name : 'field',
            length : 255,
            value : config.field ? config.field : null
        });

        return [this.classificationStoreField];
    },

    getSetterData: function () {
        return {
            'keyConfig': this.toColumn.data.config.keyId,
            'groupConfig': this.toColumn.data.config.groupId,
            'field': this.classificationStoreField.getValue()
        };
    }
});
