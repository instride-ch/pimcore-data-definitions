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

interface MappingInterface
{
    /**
     * @return string|null
     */
    public function getToColumn();

    /**
     * @param string $toColumn
     */
    public function setToColumn($toColumn);

    /**
     * @return string|null
     */
    public function getFromColumn();

    /**
     * @param string $fromColumn
     */
    public function setFromColumn($fromColumn);

    /**
     * @return string|null
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
