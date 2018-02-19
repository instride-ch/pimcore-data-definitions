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

pimcore.registerNS('pimcore.plugin.importdefinitions.setters.objectbrick');

pimcore.plugin.importdefinitions.setters.objectbrick = Class.create(pimcore.plugin.importdefinitions.setters.abstract, {

    getLayout: function (fromColumn, toColumn, record, config, definitionConfig) {
        this.toColumn = toColumn;

        var possibleFields = [];
        var fieldClass = toColumn.data.config.class;

        Ext.Object.each(definitionConfig.bricks, function (key, value) {
            if (value.indexOf(fieldClass) >= 0) {
                possibleFields.push(key);
            }
        });

        this.fieldCombo = Ext.create({
            xtype: 'combo',
            fieldLabel: t('field'),
            name: 'brickField',
            value: config.brickField ? config.brickField : null,
            store: possibleFields,
            triggerAction: 'all',
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: 'local'
        });

        return [this.fieldCombo];
    },

    getSetterData: function () {
        return {
            'class': this.toColumn.data.config.class,
            'brickField': this.fieldCombo.getValue()
        };
    }
});
