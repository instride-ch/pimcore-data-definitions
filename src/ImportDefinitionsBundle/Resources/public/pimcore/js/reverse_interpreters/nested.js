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

pimcore.registerNS('pimcore.plugin.importdefinitions.reverse_interpreters.nested');

pimcore.plugin.importdefinitions.reverse_interpreters.nested = Class.create(pimcore.plugin.importdefinitions.interpreters.nested, {
    getStore: function() {
        return pimcore.globalmanager.get('importdefinitions_reverse_interpreters');
    },

    getClassItem: function() {
        return pimcore.plugin.importdefinitions.reverse_interpreters;
    },

    getInterpreterIdentifier: function(interpreter) {
        return interpreter.get('reverseInterpreter');
    },
});
