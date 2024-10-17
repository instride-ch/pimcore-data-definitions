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

namespace Instride\Bundle\DataDefinitionsBundle\Model\ExportMapping;

use Instride\Bundle\DataDefinitionsBundle\Model\AbstractColumn;

class FromColumn extends AbstractColumn
{
    /**
     * @var string|null
     */
    public $type;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string|null
     */
    public $fieldtype;

    /**
     * @var array|null
     */
    public $config;

    /**
     * @var string|null
     */
    public $interpreter;

    /**
     * @var array|null
     */
    public $interpreterConfig;

    /**
     * @var string|null
     */
    public $getter;

    /**
     * @var array|null
     */
    public $getterConfig;

    /**
     * @var string|null
     */
    public $group;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getFieldtype()
    {
        return $this->fieldtype;
    }

    /**
     * @param string $fieldtype
     */
    public function setFieldtype($fieldtype)
    {
        $this->fieldtype = $fieldtype;
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
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

    /**
     * @return string|null
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
     * @return string|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(string $group)
    {
        $this->group = $group;
    }
}
