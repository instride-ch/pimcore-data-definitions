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

namespace Wvision\Bundle\DataDefinitionsBundle\Loader;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

interface LoaderInterface
{
    /**
     * @param string                  $class
     * @param                         $data
     * @param DataDefinitionInterface $definition
     * @param                         $params
     *
     * @return null|Concrete
     */
    public function load(string $class, $data, DataDefinitionInterface $definition, $params): ?Concrete;
}


