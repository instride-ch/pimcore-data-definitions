<?php

declare(strict_types=1);

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

namespace Instride\Bundle\DataDefinitionsBundle\Rules\Action;

use Instride\Bundle\DataDefinitionsBundle\Rules\Model\ImportRuleInterface;
use Pimcore\Model\DataObject\Concrete;

interface ImportRuleProcessorInterface
{
    public function apply(
        ImportRuleInterface $rule,
        Concrete $concrete,
        $value,
        array $configuration,
        array $params = [],
    );
}
