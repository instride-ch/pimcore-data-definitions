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
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Setter;

use Wvision\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Getter\GetterInterface;

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
