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

use Pimcore\Model\DataObject;
use Wvision\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;

class KeySetter implements SetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $setter = explode('~', $context->getImportMapping()->getToColumn());
        $setter = preg_replace('/^o_/', '', $setter[0]);
        $setter = sprintf('set%s', ucfirst($setter));

        if (method_exists($context->getObject(), $setter)) {
            $context->getObject()->$setter(DataObject\Service::getValidKey($context->getValue(), "object"));
        }
    }
}
