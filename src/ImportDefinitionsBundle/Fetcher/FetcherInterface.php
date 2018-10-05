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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Fetcher;

use ImportDefinitionsBundle\Model\DefinitionInterface;

interface FetcherInterface
{
    /**
     * @param DefinitionInterface $definition
     * @param                     $params
     * @param int                 $limit
     * @param int                 $offset
     * @return mixed
     */
    public function fetch(DefinitionInterface $definition, $params, int $limit, int $offset);

    /**+
     * @param DefinitionInterface $definition
     * @param                     $params
     * @return int
     */
    public function count(DefinitionInterface $definition, $params): int;
}