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

namespace Wvision\Bundle\DataDefinitionsBundle\Model\Log;

use Exception;
use Pimcore\Model;
use Pimcore\Model\Paginator\PaginateListingInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\Log;
use function in_array;

class Listing extends Model\Listing\AbstractListing implements PaginateListingInterface
{
    public ?array $data;

    /**
     * @var string
     */
    public $locale;

    /**
     * List of valid order keys.
     */
    public array $validOrderKeys = ['id'];

    /**
     * Test if the passed key is valid.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isValidOrderKey($key): bool
    {
        return in_array($key, $this->validOrderKeys, true);
    }

    /**
     * @return Log[]
     * @throws Exception
     */
    public function getObjects()
    {
        if (null === $this->data) {
            $this->load();
        }

        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setObjects($data)
    {
        $this->data = $data;
    }

    /** Methods for AdapterInterface */

    /**
     * Get total count
     *
     * @return int
     * @throws Exception
     */
    public function count(): int
    {
        return $this->getTotalCount();
    }

    /**
     * Get all items
     *
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array
     * @throws Exception
     */
    public function getItems($offset, $itemCountPerPage): array
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->load();
    }

    /**
     * Get Paginator Adapter
     *
     * @return $this
     */
    public function getPaginatorAdapter()
    {
        return $this;
    }

    /**
     * Set Locale
     *
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get Locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Methods for Iterator
     */

    /**
     * Rewind
     *
     * @throws Exception
     */
    public function rewind(): void
    {
        $this->getData();
        reset($this->data);
    }

    /**
     * Current
     *
     * @return mixed
     * @throws Exception
     */
    public function current(): mixed
    {
        $this->getData();

        return current($this->data);
    }

    /**
     * Key
     *
     * @return int|string|null
     * @throws Exception
     */
    public function key(): int|string|null
    {
        $this->getData();

        return key($this->data);
    }

    /**
     * Next
     *
     * @return void
     * @throws Exception
     */
    public function next(): void
    {
        $this->getData();

        next($this->data);
    }

    /**
     * Valid
     *
     * @return bool
     * @throws Exception
     */
    public function valid(): bool
    {
        $this->getData();

        return $this->current() !== false;
    }
}
