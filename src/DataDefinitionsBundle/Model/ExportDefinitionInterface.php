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

interface ExportDefinitionInterface extends DataDefinitionInterface
{
    /**
     * @param bool $enableInheritance
     */
    public function setEnableInheritance(bool $enableInheritance): void;

    /**
     * @return bool
     */
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

    /**
     * @param bool $fetchUnpublushed
     */
    public function setFetchUnpublished(bool $fetchUnpublushed): void;

    /**
     * @return bool
     */
    public function isFetchUnpublished(): bool;
}
