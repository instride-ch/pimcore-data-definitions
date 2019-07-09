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

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

class ExportMapping extends AbstractMapping
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var array
     */
    public $getterConfig;

    /**
     * @return null|string
     */
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * @param string $getter
     */
    public function setGetter($getter)
    {
        $this->getter = $getter;
    }

    /**
     * @return array|null
     */
    public function getGetterConfig()
    {
        return $this->getterConfig;
    }

    /**
     * @param array $getterConfig
     */
    public function setGetterConfig($getterConfig)
    {
        $this->getterConfig = $getterConfig;
    }
}


