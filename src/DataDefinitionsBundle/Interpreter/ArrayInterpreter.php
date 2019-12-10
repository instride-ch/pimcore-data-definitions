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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class ArrayInterpreter implements InterpreterInterface
{    
    /**
     * {@inheritdoc}
     */
    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        $data,
        DataDefinitionInterface $definition,
        $params,
        $configuration
    ) {
        
        $type = $configuration['type'];
        switch ($type) {
            case 'csv':
                $value = explode($configuration['csv_separator'], $value);
                break;
            case 'json':
                $value = json_decode($value, true);
                break;
            case 'php':
                $value = unserialize($value);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Not valid data type given, given %s', $type));
        }
        
        if (!is_array($value)) {
            throw new \Exception(sprintf('Failed to interpret %s data as array.', $type));
        }

        return $value;
    }
}


