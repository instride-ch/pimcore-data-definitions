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

class ImportMapping extends AbstractMapping
{
    /**
     * @var bool
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
     * @param bool $primaryIdentifier
     */
    public function setPrimaryIdentifier($primaryIdentifier)
    {
        $this->primaryIdentifier = $primaryIdentifier;
    }

    /**
     * @return string|null
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
