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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

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

    public static function getById(string $name)
    {
        return static::getByName($name);
    }

    public static function getByName($id)
    {
        $definitionEntry = new ExportDefinition();
        $definitionEntry->setId((int)$id);
        /**
         * @var \Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinition\Dao|\Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinition\Dao
         */
        $dao = $definitionEntry->getDao();
        $dao->getByName($id);

        return $definitionEntry;
    }

    /**
     * @param bool $enableInheritance
     */
    public function setEnableInheritance(bool $enableInheritance): void
    {
        $this->enableInheritance = $enableInheritance;
    }

    /**
     * @return bool
     */
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

    /**
     * @param bool $fetchUnpublushed
     */
    public function setFetchUnpublished(bool $fetchUnpublushed): void
    {
        $this->fetchUnpublished = $fetchUnpublushed;
    }

    /**
     * @return bool
     */
    public function isFetchUnpublished(): bool
    {
        return $this->fetchUnpublished;
    }
}
