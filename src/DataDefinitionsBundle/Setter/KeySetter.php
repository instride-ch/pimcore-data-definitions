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

use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Pimcore\Model\DataObject;

class KeySetter implements SetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $setter = explode('~', $context->getMapping()->getToColumn());
        $setter = preg_replace('/^o_/', '', $setter[0]);
        $setter = sprintf('set%s', ucfirst($setter));

        if (method_exists($context->getObject(), $setter)) {
            $context->getObject()->$setter(DataObject\Service::getValidKey($context->getValue(), 'object'));
        }
    }
}
