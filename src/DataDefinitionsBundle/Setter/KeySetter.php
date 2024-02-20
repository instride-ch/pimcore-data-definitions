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

use Pimcore\Model\DataObject;
use Instride\Bundle\DataDefinitionsBundle\Context\SetterContextInterface;

class KeySetter implements SetterInterface
{
    public function set(SetterContextInterface $context): void
    {
        $setter = explode('~', $context->getMapping()->getToColumn());
        $setter = preg_replace('/^o_/', '', $setter[0]);
        $setter = sprintf('set%s', ucfirst($setter));

        if (method_exists($context->getObject(), $setter)) {
            $context->getObject()->$setter(DataObject\Service::getValidKey($context->getValue(), "object"));
        }
    }
}
