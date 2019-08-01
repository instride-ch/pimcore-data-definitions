/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.setters.classificationstore');
pimcore.registerNS('pimcore.plugin.importdefinitions.setters.classificationstore');

pimcore.plugin.datadefinitions.setters.classificationstore = Class.create(pimcore.plugin.datadefinitions.setters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        this.toColumn = toColumn;

        return [];
    },

    getSetterData: function () {
        return {
            'keyConfig': this.toColumn.data.config.keyId,
            'groupConfig': this.toColumn.data.config.groupId,
            'field': this.toColumn.data.config.field
        };
    }
});

pimcore.plugin.importdefinitions.setters.classificationstore = Class.create(pimcore.plugin.datadefinitions.setters.classificationstore, {});
