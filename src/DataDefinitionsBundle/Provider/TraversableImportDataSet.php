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

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

class TraversableImportDataSet implements ImportDataSetInterface, \Countable
{
    private \IteratorIterator $iterator;

    private int $count;

    public function __construct(
        \Traversable $iterator,
    ) {
        $this->count = iterator_count($iterator);
        $this->iterator = new \IteratorIterator($iterator);
    }

    /**
     * Return the current element
     *
     * @see https://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     *
     * @since 5.0.0
     */
    public function current(): mixed
    {
        return $this->iterator->current();
    }

    /**
     * Move forward to next element
     *
     * @see https://php.net/manual/en/iterator.next.php
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->iterator->next();
    }

    /**
     * Return the key of the current element
     *
     * @see https://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure.
     *
     * @since 5.0.0
     */
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    /**
     * Checks if current position is valid
     *
     * @see https://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @see https://php.net/manual/en/iterator.rewind.php
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    /**
     * Return the number of elements in the Iterator
     */
    public function count(): int
    {
        return $this->count;
    }
}
