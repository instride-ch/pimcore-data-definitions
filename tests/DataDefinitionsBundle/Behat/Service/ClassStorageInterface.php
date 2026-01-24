<?php
/**
 * Data Definitions.
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in LICENSE.md
 * which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
*/

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Service;

interface ClassStorageInterface
{
    public function get(string $className): string;

    public function has(string $className): bool;

    public function set(string $className): string;
}
