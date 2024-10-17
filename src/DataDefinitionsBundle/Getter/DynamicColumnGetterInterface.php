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

namespace Instride\Bundle\DataDefinitionsBundle\Getter;

use Instride\Bundle\DataDefinitionsBundle\Context\GetterContextInterface;

interface DynamicColumnGetterInterface extends GetterInterface
{
    /**
     * @inheritDoc
     *
     * @return array The key-value array will be merged into the final data set,
     *               with array keys becoming column names.
     *
     *               It's up to the developer to ensure the keys don't collide
     *               with other columns from the definition and to always return
     *               exactly the same keys in exactly the same order for each object.
     */
    public function get(GetterContextInterface $context): array;
}
