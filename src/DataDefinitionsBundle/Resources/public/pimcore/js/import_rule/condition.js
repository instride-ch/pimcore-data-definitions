/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

pimcore.registerNS('pimcore.plugin.datadefinitions.import_rule.condition');

pimcore.plugin.datadefinitions.import_rule.condition = Class.create(coreshop.rules.condition, {
    initialize: function (conditions, type) {
        this.conditions = conditions;
        this.type = type;
    },

    getConditionClassNamespace: function () {
        return pimcore.plugin.datadefinitions.import_rule.conditions;
    }
});
