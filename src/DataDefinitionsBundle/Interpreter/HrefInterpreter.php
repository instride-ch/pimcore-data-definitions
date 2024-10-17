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

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\Service;
use Pimcore\Tool;

class HrefInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        $type = $context->getConfiguration()['type'] ?: 'object';
        $objectClass = $context->getConfiguration()['class'];

        if (!$context->getValue()) {
            return null;
        }

        if ($type === 'object' && $objectClass) {
            $class = 'Pimcore\Model\DataObject\\' . $objectClass;

            if (!Tool::classExists($class)) {
                $class = 'Pimcore\Model\DataObject\\' . ucfirst($objectClass);
            }

            if (Tool::classExists($class)) {
                $class = new $class();

                if ($class instanceof Concrete) {
                    $ret = $class::getById($context->getValue());

                    if ($ret instanceof Concrete) {
                        return $ret;
                    }
                }
            }
        } else {
            return Service::getElementById($type, $context->getValue());
        }

        return null;
    }
}
