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

class ImportMapping extends AbstractMapping
{
    /**
     * @var boolean
     */
    public $primaryIdentifier;

    /**
     * @var string
     */
    public $setter;

    /**
     * @var array
     */
    public $setterConfig;

    /**
     * @return bool|null
     */
    public function getPrimaryIdentifier()
    {
        return $this->primaryIdentifier;
    }

    /**
     * @param boolean $primaryIdentifier
     */
    public function setPrimaryIdentifier($primaryIdentifier)
    {
        $this->primaryIdentifier = $primaryIdentifier;
    }

    /**
     * @return null|string
     */
    public function getSetter()
    {
        return $this->setter;
    }

    /**
     * @param string $setter
     */
    public function setSetter($setter)
    {
        $this->setter = $setter;
    }

    /**
     * @return array|null
     */
    public function getSetterConfig()
    {
        return $this->setterConfig;
    }

    /**
     * @param array $setterConfig
     */
    public function setSetterConfig($setterConfig)
    {
        $this->setterConfig = $setterConfig;
    }
}
