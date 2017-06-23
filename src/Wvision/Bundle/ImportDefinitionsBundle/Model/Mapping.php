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

namespace Wvision\Bundle\ImportDefinitionsBundle\Model;

class Mapping
{
    /**
     * @var string
     */
    public $fromColumn;

    /**
     * @var string
     */
    public $toColumn;

    /**
     * @var boolean
     */
    public $primaryIdentifier;

    /**
     * @var string
     */
    public $setter;

    /**
     * @var string
     */
    public $interpreter;

    /**
     * @var array
     */
    public $interpreterConfig;

    /**
     * @var array
     */
    public $setterConfig;

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }
            
            $setter = 'set'.ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getToColumn()
    {
        return $this->toColumn;
    }

    /**
     * @param string $toColumn
     */
    public function setToColumn($toColumn)
    {
        $this->toColumn = $toColumn;
    }

    /**
     * @return string
     */
    public function getFromColumn()
    {
        return $this->fromColumn;
    }

    /**
     * @param string $fromColumn
     */
    public function setFromColumn($fromColumn)
    {
        $this->fromColumn = $fromColumn;
    }

    /**
     * @return boolean
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
     * @return string
     */
    public function getSetter()
    {
        return $this->setter;
    }

    /**
     * @param string $setter
     */
    public function setSetter(string $setter)
    {
        $this->setter = $setter;
    }

    /**
     * @return string
     */
    public function getInterpreter()
    {
        return $this->interpreter;
    }

    /**
     * @param string $interpreter
     */
    public function setInterpreter(string $interpreter)
    {
        $this->interpreter = $interpreter;
    }

    /**
     * @return array
     */
    public function getInterpreterConfig()
    {
        return $this->interpreterConfig;
    }

    /**
     * @param array $interpreterConfig
     */
    public function setInterpreterConfig($interpreterConfig)
    {
        $this->interpreterConfig = $interpreterConfig;
    }

    /**
     * @return array
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
