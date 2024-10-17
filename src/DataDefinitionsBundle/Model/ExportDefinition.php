<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model;

/**
 * @method ExportDefinition\Dao getDao()
 */
class ExportDefinition extends AbstractDataDefinition implements ExportDefinitionInterface
{
    /**
     * @var bool
     */
    public $enableInheritance = true;

    /**
     * @var string
     */
    public $fetcher;

    /**
     * @var array
     */
    public $fetcherConfig;

    /**
     * @var bool
     */
    public $fetchUnpublished = false;

    public static function getById(int $id): self
    {
        $definitionEntry = new self();
        $dao = $definitionEntry->getDao();
        $dao->getById((string) $id);

        return $definitionEntry;
    }

    public static function getByName(string $name): self
    {
        $definitionEntry = new self();
        $dao = $definitionEntry->getDao();
        $dao->getByName($name);

        return $definitionEntry;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEnableInheritance(bool $enableInheritance): void
    {
        $this->enableInheritance = $enableInheritance;
    }

    public function isEnableInheritance(): bool
    {
        return $this->enableInheritance;
    }

    public function getFetcher()
    {
        return $this->fetcher;
    }

    public function setFetcher($fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function getFetcherConfig()
    {
        return $this->fetcherConfig;
    }

    public function setFetcherConfig($fetcherConfig)
    {
        $this->fetcherConfig = $fetcherConfig;
    }

    public function setFetchUnpublished(bool $fetchUnpublushed): void
    {
        $this->fetchUnpublished = $fetchUnpublushed;
    }

    public function isFetchUnpublished(): bool
    {
        return $this->fetchUnpublished;
    }
}
