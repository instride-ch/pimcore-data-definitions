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

namespace ImportDefinitionsBundle\Model;

interface MappingInterface
{
    /**
     * @return null|string
     */
    public function getToColumn();
    /**
     * @param string $toColumn
     */
    public function setToColumn($toColumn);

    /**
     * @return null|string
     */
    public function getFromColumn();

    /**
     * @param string $fromColumn
     */
    public function setFromColumn($fromColumn);

    /**
     * @return null|string
     */
    public function getInterpreter();

    /**
     * @param string $interpreter
     */
    public function setInterpreter($interpreter);

    /**
     * @return array|null
     */
    public function getInterpreterConfig();

    /**
     * @param array $interpreterConfig
     */
    public function setInterpreterConfig($interpreterConfig);
}

class_alias(MappingInterface::class, 'ImportDefinitionsBundle\Model\Mapping');