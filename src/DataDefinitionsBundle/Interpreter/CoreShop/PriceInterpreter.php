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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter\CoreShop;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use function is_string;

final class PriceInterpreter implements InterpreterInterface
{
    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        array $data,
        DataDefinitionInterface $definition,
        array $params,
        array $configuration
    ) {
        $inputIsFloat = $configuration['isFloat'];

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
            $value = (float)$value;
        }

        if ($inputIsFloat) {
            $value = (int)round(round($value, 2) * 100, 0);
        }

        return (int)$value;
    }
}


