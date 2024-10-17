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

use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

class ObjectbrickSetter implements SetterInterface, GetterInterface
{
    public function set(SetterContextInterface $context)
    {
        $keyParts = explode('~', $context->getMapping()->getToColumn());

        $config = $context->getMapping()->getSetterConfig();
        $fieldName = $config['brickField'];
        $class = $config['class'];
        $brickField = $keyParts[3];

        $brickGetter = sprintf('get%s', ucfirst($fieldName));
        $brickSetter = sprintf('set%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $brickGetter)) {
            $brick = $context->getObject()->$brickGetter();

            if (!$brick instanceof Objectbrick) {
                $brick = new Objectbrick($context->getObject(), $fieldName);
                $context->getObject()->$brickSetter($brick);
            }

            if ($brick instanceof Objectbrick) {
                $brickClassGetter = sprintf('get%s', ucfirst($class));
                $brickClassSetter = sprintf('set%s', ucfirst($class));

                $brickFieldObject = $brick->$brickClassGetter();

                if (!$brickFieldObject instanceof AbstractData) {
                    $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\' . $class;

                    $brickFieldObject = new $brickFieldObjectClass($context->getObject());

                    $brick->$brickClassSetter($brickFieldObject);
                }

                $setter = sprintf('set%s', ucfirst($brickField));

                if (method_exists($brickFieldObject, $setter)) {
                    $brickFieldObject->$setter($context->getValue());
                }
            }
        }
    }

    public function get(GetterContextInterface $context)
    {
        $keyParts = explode('~', $context->getMapping()->getFromColumn());

        $config = $context->getMapping()->getGetterConfig();
        $fieldName = $config['brickField'];
        $class = $config['class'];
        $brickField = $keyParts[3];

        $brickGetter = sprintf('get%s', ucfirst($fieldName));

        if (method_exists($context->getObject(), $brickGetter)) {
            $brick = $context->getObject()->$brickGetter();

            if (!$brick instanceof Objectbrick) {
                return;
            }

            $brickClassGetter = sprintf('get%s', ucfirst($class));
            $brickClassSetter = sprintf('set%s', ucfirst($class));

            $brickFieldObject = $brick->$brickClassGetter();

            if (!$brickFieldObject instanceof AbstractData) {
                $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\' . $class;

                $brickFieldObject = new $brickFieldObjectClass($context->getObject());

                $brick->$brickClassSetter($brickFieldObject);
            }

            $getter = sprintf('get%s', ucfirst($brickField));

            if (method_exists($brickFieldObject, $getter)) {
                return $brickFieldObject->$getter();
            }
        }

        return null;
    }
}
