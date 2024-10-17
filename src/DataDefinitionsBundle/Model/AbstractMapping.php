<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model;

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
     * @return string|null
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
     * @return string|null
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
     * @return string|null
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
