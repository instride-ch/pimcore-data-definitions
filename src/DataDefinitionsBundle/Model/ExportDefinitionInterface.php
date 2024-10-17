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

interface ExportDefinitionInterface extends DataDefinitionInterface
{
    public function setEnableInheritance(bool $enableInheritance): void;

    public function isEnableInheritance(): bool;

    /**
     * @return mixed
     */
    public function getFetcher();

    /**
     * @param string $fetcher
     */
    public function setFetcher($fetcher);

    /**
     * @return array
     */
    public function getFetcherConfig();

    /**
     * @param array $fetcherConfig
     */
    public function setFetcherConfig($fetcherConfig);

    public function setFetchUnpublished(bool $fetchUnpublushed): void;

    public function isFetchUnpublished(): bool;
}
