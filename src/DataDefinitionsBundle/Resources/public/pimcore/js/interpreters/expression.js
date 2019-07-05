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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.expression');

pimcore.plugin.datadefinitions.interpreters.expression = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textfield',
            fieldLabel: t('data_definitions_interpreter_expression'),
            name: 'expression',
            width: 500,
            value : config.expression ? config.expression : null
        }];
    }
});
