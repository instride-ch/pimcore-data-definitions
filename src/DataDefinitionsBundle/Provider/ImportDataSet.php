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

namespace Instride\Bundle\DataDefinitionsBundle\Provider;

use Closure;
use Iterator;

class ImportDataSet implements ImportDataSetInterface, \Countable
{
    private Iterator $iterator;

    private int|false $countAll;

    private ?Closure $processor;

    public function __construct(
        Iterator $iterator,
        Closure $processor = null,
    ) {
        $this->iterator = $iterator;
        $this->countAll = false;
        $this->processor = $processor ?? static function ($current) {
            return $current;
        };
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
        return ($this->processor)($this->iterator->current());
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
     * Count elements of an object
     *
     * @see https://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     *
     * @since 5.1.0
     */
    public function count(): int
    {
        if (false === $this->countAll) {
            $this->rewind();
            $this->countAll = iterator_count($this->iterator);
        }

        return $this->countAll;
    }
}
