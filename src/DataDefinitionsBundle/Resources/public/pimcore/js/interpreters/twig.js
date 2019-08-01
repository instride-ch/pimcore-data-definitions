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

pimcore.registerNS('pimcore.plugin.datadefinitions.interpreters.twig');
pimcore.registerNS('pimcore.plugin.importdefinitions.interpreters.twig');

pimcore.plugin.datadefinitions.interpreters.twig = Class.create(pimcore.plugin.datadefinitions.interpreters.abstract, {
    getLayout : function (fromColumn, toColumn, record, config) {
        return [{
            xtype : 'textarea',
            fieldLabel: t('data_definitions_interpreter_template'),
            name: 'template',
            width: '100%',
            value : config.template ? config.template : null
        }];
    }
});

pimcore.plugin.importdefinitions.interpreters.twig = Class.create(pimcore.plugin.datadefinitions.interpreters.twig, {});
