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

class ExportDefinition extends AbstractDataDefinition implements ExportDefinitionInterface
{
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

    /**
     * {@inheritdoc}
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setFetcher($fetcher)
    {
        $this->fetcher = $fetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getFetcherConfig()
    {
        return $this->fetcherConfig;
    }

    /**
     * {@inheritdoc}
     */
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
