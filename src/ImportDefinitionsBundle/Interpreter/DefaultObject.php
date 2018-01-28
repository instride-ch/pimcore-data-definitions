<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2017 Divante (http://www.divante.co)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Interpreter;

use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class DefaultObject
 *
 * @package ImportDefinitionsBundle\Interpreter
 */
class DefaultObject implements InterpreterInterface
{

    /**
     * @param Concrete $object
     * @param mixed $value
     * @param Mapping $map
     * @param array $data
     * @param DefinitionInterface $definition
     * @param array $params
     * @param array $configuration
     *
     * @return mixed
     */
    public function interpret(
        Concrete $object,
        $value,
        Mapping $map,
        $data,
        DefinitionInterface $definition,
        $params,
        $configuration
    ) {
        $path = $configuration['path'];

        $object = DataObject::getByPath($path);

        if ($object) {
            return $object;
        }

        return false;
    }
}
