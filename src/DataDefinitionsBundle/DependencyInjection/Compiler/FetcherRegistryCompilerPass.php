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

namespace Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class FetcherRegistryCompilerPass extends RegisterRegistryTypePass
{
    public const FETCHER_TAG = 'data_definitions.fetcher';

    public function __construct(
        ) {
        parent::__construct(
            'data_definitions.registry.fetcher',
            'data_definitions.form.registry.fetcher',
            'data_definitions.fetchers',
            self::FETCHER_TAG,
        );
    }
}
