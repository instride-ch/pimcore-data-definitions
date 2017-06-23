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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\ImportDefinitionsBundle\Runner;

use Pimcore\Model\Object\Concrete;
use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;

interface RunnerInterface
{
    /**
     * @param Concrete $object
     * @param array $data
     * @param DefinitionInterface $definition
     * @param array $params
     */
    public function preRun(Concrete $object, $data, DefinitionInterface $definition, $params);

    /**
     * @param Concrete $object
     * @param array $data
     * @param DefinitionInterface $definition
     * @param array $params
     */
    public function postRun(Concrete $object, $data, DefinitionInterface $definition, $params);
}