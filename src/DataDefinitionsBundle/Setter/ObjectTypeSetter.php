<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Setter;

use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Pimcore\Model\DataObject\Concrete;

class ObjectTypeSetter implements SetterInterface
{
    public function set(SetterContextInterface $context)
    {
        if ($context->getValue() === Concrete::OBJECT_TYPE_FOLDER) {
            $context->getObject()->setType(Concrete::OBJECT_TYPE_FOLDER);

            return;
        }

        if ($context->getValue() === Concrete::OBJECT_TYPE_VARIANT) {
            $context->getObject()->setType(Concrete::OBJECT_TYPE_VARIANT);

            return;
        }

        $context->getObject()->setType(Concrete::OBJECT_TYPE_OBJECT);
    }
}
