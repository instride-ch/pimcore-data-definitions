<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Model\Log;

use Pimcore\Model;
use ImportDefinitionsBundle\Model\Log;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\AdapterAggregateInterface;

class Listing extends Model\Listing\AbstractListing implements AdapterInterface, AdapterAggregateInterface, \Iterator
{
    /**
     * List of Logs.
     *
     * @var array
     */
    public $data;

    /**
     * @var string
     */
    public $locale;

    /**
     * List of valid order keys.
     *
     * @var array
     */
    public $validOrderKeys = ['id'];

    /**
     * Test if the passed key is valid.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isValidOrderKey($key)
    {
        return \in_array($key, $this->validOrderKeys, true);
    }

    /**
     * @return Log[]
     * @throws \Exception
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
     * @return mixed
     * @throws \Exception
     */
    public function count()
    {
        return $this->getTotalCount();
    }

    /**
     * Get all items
     *
     * @param int $offset
     * @param int $itemCountPerPage
     * @return mixed
     * @throws \Exception
     */
    public function getItems($offset, $itemCountPerPage)
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
     * @throws \Exception
     */
    public function rewind()
    {
        $this->getData();
        reset($this->data);
    }

    /**
     * Current
     *
     * @return mixed
     * @throws \Exception
     */
    public function current()
    {
        $this->getData();

        return current($this->data);
    }

    /**
     * Key
     *
     * @return mixed
     * @throws \Exception
     */
    public function key()
    {
        $this->getData();

        return key($this->data);
    }

    /**
     * Next
     *
     * @return mixed
     * @throws \Exception
     */
    public function next()
    {
        $this->getData();

        return next($this->data);
    }

    /**
     * Valid
     *
     * @return bool
     * @throws \Exception
     */
    public function valid()
    {
        $this->getData();

        return $this->current() !== false;
    }
}
