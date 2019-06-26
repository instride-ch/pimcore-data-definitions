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

namespace WVision\Bundle\DataDefinitionsBundle\Model;

abstract class AbstractMapping implements MappingInterface
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
     * @var string
     */
    public $interpreter;

    /**
     * @var array
     */
    public $interpreterConfig;

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key === 'o_type') {
                continue;
            }

            $setter = sprintf('set%s', ucfirst($key));

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return null|string
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
     * @return null|string
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
     * @return null|string
     */
    public function getInterpreter()
    {
        return $this->interpreter;
    }

    /**
     * @param string $interpreter
     */
    public function setInterpreter($interpreter)
    {
        $this->interpreter = $interpreter;
    }

    /**
     * @return array|null
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
}

class_alias(AbstractMapping::class, 'ImportDefinitionsBundle\Model\AbstractMapping');
