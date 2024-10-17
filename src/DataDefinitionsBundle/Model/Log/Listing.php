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

namespace Instride\Bundle\DataDefinitionsBundle\Model\Log;

use Exception;
use function in_array;
use Instride\Bundle\DataDefinitionsBundle\Model\Log;
use Pimcore\Model;
use Pimcore\Model\Paginator\PaginateListingInterface;

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
     */
    public function isValidOrderKey($key): bool
    {
        return in_array($key, $this->validOrderKeys, true);
    }

    /**
     * @return Log[]
     *
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
     *
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
     * @throws Exception
     */
    public function valid(): bool
    {
        $this->getData();

        return $this->current() !== false;
    }
}
