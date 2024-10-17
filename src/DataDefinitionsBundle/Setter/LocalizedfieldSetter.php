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

namespace Instride\Bundle\DataDefinitionsBundle\Setter;

use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;

class LocalizedfieldSetter implements SetterInterface, GetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $config = $context->getMapping()->getSetterConfig();

        $setter = explode('~', $context->getMapping()->getToColumn());
        $setter = sprintf('set%s', ucfirst($setter[0]));

        if (method_exists($context->getObject(), $setter)) {
            $context->getObject()->$setter($context->getValue(), $config['language']);
        }
    }

    public function get(GetterContextInterface $context)
    {
        $config = $context->getMapping()->getGetterConfig();

        $getter = explode('~', $context->getMapping()->getFromColumn());
        $getter = sprintf('get%s', ucfirst($getter[0]));

        if (method_exists($context->getObject(), $getter)) {
            return $context->getObject()->$getter($config['language']);
        }

        return null;
    }
}
