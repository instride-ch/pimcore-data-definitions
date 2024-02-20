<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Setter;

use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Getter\GetterInterface;

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
                    $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\'.$class;

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
                $brickFieldObjectClass = 'Pimcore\Model\DataObject\Objectbrick\Data\\'.$class;

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
