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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Runner;

use Wvision\Bundle\DataDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;

interface SetterRunnerInterface extends RunnerInterface
{
    /**
     * @param Concrete $object
     * @param Mapping $map
     * @param $value
     * @param $data
     * @param DefinitionInterface $definition
     * @param $params
     * @return mixed
     */
    public function shouldSetField(Concrete $object, Mapping $map, $value, $data, DefinitionInterface $definition, $params);
}

class_alias(SetterRunnerInterface::class, 'ImportDefinitionsBundle\Runner\SetterRunnerInterface');
